@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-mars-500 focus:ring-mars-500 rounded-md shadow-sm']) }}>
