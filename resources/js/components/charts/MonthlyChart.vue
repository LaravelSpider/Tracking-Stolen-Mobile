<template>
    <div class="w-full h-64">
        <canvas ref="chartCanvas" class="w-full h-full"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const props = defineProps({
    data: {
        type: Array,
        default: () => [],
    },
})

const chartCanvas = ref(null)
let chart = null

const createChart = () => {
    if (!chartCanvas.value || !props.data.length) return

    const ctx = chartCanvas.value.getContext('2d')

    // Simple chart implementation without Chart.js
    const canvas = chartCanvas.value
    const width = canvas.width = canvas.offsetWidth
    const height = canvas.height = canvas.offsetHeight

    ctx.clearRect(0, 0, width, height)

    // Chart styling
    const padding = 40
    const chartWidth = width - (padding * 2)
    const chartHeight = height - (padding * 2)

    // Find max value
    const maxValue = Math.max(...props.data.map(d => d.count || 0))

    // Draw axes
    ctx.strokeStyle = '#e5e7eb'
    ctx.lineWidth = 1

    // Y-axis
    ctx.beginPath()
    ctx.moveTo(padding, padding)
    ctx.lineTo(padding, height - padding)
    ctx.stroke()

    // X-axis
    ctx.beginPath()
    ctx.moveTo(padding, height - padding)
    ctx.lineTo(width - padding, height - padding)
    ctx.stroke()

    // Draw data
    if (props.data.length > 0) {
        const barWidth = chartWidth / props.data.length * 0.8
        const barSpacing = chartWidth / props.data.length * 0.2

        ctx.fillStyle = '#3b82f6'

        props.data.forEach((item, index) => {
            const barHeight = (item.count / maxValue) * chartHeight
            const x = padding + (index * (barWidth + barSpacing)) + barSpacing / 2
            const y = height - padding - barHeight

            ctx.fillRect(x, y, barWidth, barHeight)

            // Draw labels
            ctx.fillStyle = '#6b7280'
            ctx.font = '12px sans-serif'
            ctx.textAlign = 'center'
            ctx.fillText(item.month, x + barWidth / 2, height - padding + 20)

            ctx.fillStyle = '#3b82f6'
        })
    }
}

onMounted(() => {
    createChart()
})

watch(() => props.data, () => {
    createChart()
}, { deep: true })
</script>
