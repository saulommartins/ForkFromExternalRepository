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
* Função para abrir a popup de patrimônio de dentro da GP
* e eliminar dependência com a GA (provisória)
* Data de Criação: 16/06/2006


* @author Analista: Diego Barbosa :Victoria
* @author Desenvolvedor: Fernando Zank Correa Evangelista

$Revision$
$Name$
$Author$
$Date$

Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.4  2006/07/06 13:57:50  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:08:22  diego


*/

?>
<script>
/*********************************************************************************
Função para abrir janela de Procura BEM
*********************************************************************************
Exemplo:  procuraBem("frm","codbem")
*/
function procuraBemGP(nomeform,campobem,sessao){
    var x = 200;
    var y = 120;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/popups/bem/procuraBem.php?'+sessao+'&nomForm='+nomeform+'&campoBem='+campobem;
    var wVolta=false;
    var sAux = "prbem"+ sessaoid +" = window.open(sArq,'prbem"+ sessaoid +"','width=650px,height=500px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}
</script>
