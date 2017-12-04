<?php
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
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_FW_LEGADO."dataBaseLegado.class.php"    );

if (!Sessao::started()) {
   header("location:login.php?".Sessao::getId()."&erro=2");
}
//Array de status
//$sSQL = "select parametro,valor from administracao.configuracao WHERE parametro = 'mensagem' and exercicio = '".Sessao::getExercicio()."'";
//$dbEmp = new dataBaseLegado;
//$dbEmp->abreBD();
//$dbEmp->abreSelecao($sSQL);
//$dbEmp->vaiPrimeiro();
//$janela="";
//while (!$dbEmp->eof()) {
//   $divulga  = strip_tags(trim($dbEmp->pegaCampo("valor")));
//   $dbEmp->vaiProximo();
//   $janela .= "$divulga";
//}
//$dbEmp->limpaSelecao();
//$dbEmp->fechaBD();
?>
<script laguage='Javascript'>
    parent.frames["telaStatus"].location.replace('status.php');

</script>
<table width="100%" style="background-color: #EDF4FA">
    <tr>
        <td><img src="<?=CAM_FW_IMAGENS;?>loading_modal.gif" style="display: none"/></td>
        <td><img src="<?=CAM_FW_IMAGENS;?>logo_urbem_grande.png" border=0></td>
        <td>&nbsp;&nbsp;</td>
        <td>
        <font color="#000000" face="Futura, Arial, Helvetica" size=2><b>
        Pacotes Urbem Instalados:<br>
        <?php
        if( defined('VERSAO_GA') )
          echo "- Gestão Administrativa: ".VERSAO_GA."<br>\n";
        if( defined('VERSAO_GF') )
          echo "- Gestão Financeira: ".VERSAO_GF."<br>\n";
        if( defined('VERSAO_GP') )
          echo "- Gestão Patrimonial: ".VERSAO_GP."<br>\n";
        if( defined('VERSAO_GRH') )
          echo "- Gestão Recursos Humanos: ".VERSAO_GRH."<br>\n";
        if( defined('VERSAO_GT') )
          echo "- Gestão Tributária: ".VERSAO_GT."<br>\n";
        if( defined('VERSAO_GPC') )
          echo "- Gestão Prestação de Contas: ".VERSAO_GPC."<br>\n";
        echo "Recomenda-se que o cache do navegador seja limpo.<br>";
        ?>
        <?//=$janela;?>
        </b></font>
        </td>
    </tr>
</table>
<?php
if (isset($_REQUEST['reservaSaldo'])) {
?>
<div id="dialog"> <!-- Data da Reserva de Saldo -->
   <input type="hidden" id="stDataReserva"/>
</div>
<script>
    jq("#stDataReserva").val('31/12/<?=date("Y")-1?>');

    jq(document).ready(function () {
        window.parent.frames['telaMenu'].location = 'menu.php?'+'<?=Sessao::getId()?>'+'&nivel=1&cod_gestao_pass=2&stTitulo=Financeira&stDataReserva='+jq("#stDataReserva").val();
    });
</script>
<?php
}

//Acha caminho principal do siam web

$sDir  = dirname($_SERVER["SCRIPT_NAME"]);
Sessao::write('raiz', $sDir."/");
