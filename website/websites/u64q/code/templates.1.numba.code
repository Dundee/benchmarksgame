
import sys

from jinja2 import Template
from numba import jit

templ = """
<ul>
{% for user in users %}
  <li><a href="/user/{{ user.user_id }}">{{ user.username }}</a></li>
{% endfor %}
</ul>
"""


class User(object):
    __slots__ = ('user_id', 'username')

    def __init__(self, user_id, username):
        self.user_id = user_id
        self.username = username


@jit
def render_template(user_id):
    users = [
        User(user_id, 'SomeUsername')
    ]

    template = Template(templ)
    return template.render(users=users)


def main():
    n = int(sys.argv[1])

    for i in range(n):
        res = render_template(i)

    print(res)

main()
