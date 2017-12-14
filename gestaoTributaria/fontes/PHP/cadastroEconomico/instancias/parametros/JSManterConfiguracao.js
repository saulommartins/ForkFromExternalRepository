<script type="text/javascript">
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
</script>
<?
/**
    * Página de funções javascript para o cadastro de corretagem
    * Data de Criação   : 05/08/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo b. Paulino

    * @ignore

	* $Id: JSManterConfiguracao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.02.01
*/

/*
$Log$
Revision 1.3  2006/09/15 14:33:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

//retorna FALSE caso seja digitado ESPAÇO
function validaTecla( evento ){
    var teclaPressionada;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    if( teclaPressionada == 32 )
        return false;
    return true;
}

</script>
