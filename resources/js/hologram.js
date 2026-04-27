import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

export function initHologram(containerId, modelUrl) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const width = container.clientWidth;
    const height = container.clientHeight;

    const scene = new THREE.Scene();
    // Noir très sombre transparent
    scene.background = null; 
    scene.fog = new THREE.FogExp2(0x111827, 0.05);

    const camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
    camera.position.set(0, 1.5, 4);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);

    // Lumière futuriste martienne (Orange principal + Cyan backlight)
    const mainLight = new THREE.PointLight(0xfd4d0c, 2.5, 10);
    mainLight.position.set(2, 3, 2);
    scene.add(mainLight);

    const backLight = new THREE.PointLight(0x0ea5e9, 1.5, 10);
    backLight.position.set(-2, 0, -2);
    scene.add(backLight);
    
    // Grille de sol (projection holographique matricielle)
    const grid = new THREE.GridHelper(4, 15, 0xfd4d0c, 0xfd4d0c);
    grid.material.opacity = 0.15;
    grid.material.transparent = true;
    grid.position.y = -1;
    scene.add(grid);

    // Hologram central
    let hologramObj = new THREE.Group();
    scene.add(hologramObj);

    // Matériau holo
    const holoMaterial = new THREE.MeshStandardMaterial({
        color: 0xfd4d0c, // Orange mars
        emissive: 0x9c1c0b, // Lueur orange sombre
        emissiveIntensity: 0.8,
        wireframe: true,
        transparent: true,
        opacity: 0.6,
        side: THREE.DoubleSide
    });

    const loader = new GLTFLoader();
    
    loader.load(
        modelUrl,
        (gltf) => {
            const mesh = gltf.scene;
            
            // Appliquer notre matériau translucide orange partout
            mesh.traverse((child) => {
                if (child.isMesh) {
                    child.material = holoMaterial;
                }
            });

            // Ajustement rigoureux du Scale en premier
            const box1 = new THREE.Box3().setFromObject(mesh);
            const size1 = box1.getSize(new THREE.Vector3());
            const maxDim = Math.max(size1.x, size1.y, size1.z);
            if (maxDim > 0) {
                const scale = 2.8 / maxDim; // Hauteur maximale visible
                mesh.scale.set(scale, scale, scale);
            }

            // Forcer l'actualisation des matrices avec la nouvelle taille
            mesh.updateMatrixWorld(true);

            // Recalculer le centrage après le changement d'échelle
            const box2 = new THREE.Box3().setFromObject(mesh);
            const center2 = box2.getCenter(new THREE.Vector3());
            
            // Aligner le centre géométrique exact à l'origine locale (0,0,0)
            mesh.position.x -= center2.x;
            mesh.position.y -= center2.y;
            mesh.position.z -= center2.z;

            // Remonter le tout pour qu'il soit bien au centre de la vision
            // au-dessus de la grille (la grille est à y = -1)
            mesh.position.y += 1.2;

            hologramObj.add(mesh);
        },
        undefined, // On progress callback
        (error) => {
            console.log("Modèle 3D introuvable à l'adresse fournie, chargement d'une capsule de simulation biométrique.");
            // Corps de secours généré (Capsule)
            const fallbackGeometry = new THREE.CapsuleGeometry(0.4, 1.2, 4, 16);
            const fallbackMesh = new THREE.Mesh(fallbackGeometry, holoMaterial);
            fallbackMesh.position.y = -0.2;
            hologramObj.add(fallbackMesh);
        }
    );

    // Animation Loop
    const clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);
        const elapsed = clock.getElapsedTime();

        // Rotation constante type scan 3D
        hologramObj.rotation.y = elapsed * 0.5;
        
        // Léger tremblement parasite/glitch pour l'effet technologique lointain
        const floatY = Math.sin(elapsed * 2) * 0.05;
        if(Math.random() > 0.98) {
            hologramObj.position.x = (Math.random() - 0.5) * 0.05; 
            holoMaterial.opacity = 0.3 + Math.random() * 0.3;
        } else {
            hologramObj.position.x = 0;
            holoMaterial.opacity = 0.6;
        }
        hologramObj.position.y = floatY;

        renderer.render(scene, camera);
    }
    animate();

    // Resize Handler
    window.addEventListener('resize', () => {
        if (!container) return;
        const w = container.clientWidth;
        const h = container.clientHeight;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
}
