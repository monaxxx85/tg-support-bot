👤 <b>{{ $full_name ?? 'нет имени' }}</b>
📱 {{ $username ? '@' . $username : '<i>нет username</i>' }}
🆔 <code>{{ $id }}</code>
🤖 {{ $is_bot ? '<b>бот</b>' : 'человек' }}
