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

$Id: topo.php 59612 2014-09-02 12:00:51Z gelson $

*/

include '../../../../../../config.php';
include URBEM_ROOT_PATH."gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php";

Sessao::open();

# Validação para redirecionar ao login, caso o exercício não esteja setado
if (!Sessao::getExercicio()) {
   echo '<html><head><script type=\'javascript\'>window.parent.location.href = \''.URBEM_ROOT_URL.'index.php?action=sair\'</script></head><body></body></html>';
   exit;
}

# Busca nome e brasão da prefeitura.
$exec_prefeitura = Sessao::read('nom_prefeitura') ? Sessao::read('nom_prefeitura') : Sessao::write('nom_prefeitura',trim(SistemaLegado::pegaConfiguracao('nom_prefeitura')));
$exec_image      = Sessao::read('logotipo') ? Sessao::read('logotipo') : Sessao::write('logotipo',trim(SistemaLegado::pegaConfiguracao('logotipo')));

$inicio = array_key_exists('inicio', $_GET) ? $_GET['inicio'] : null;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
    <style>
        TR.barraTopo{
            height: 20px ;
        }

        TD.barraTopo{
            background: url("<?=CAM_FW_IMAGENS?>barra_topo_geral.png") 0 0 repeat-x;
            height: 20px ;
        }

        a.barraTopo{
            float:left;
            margin:0 0 0 100px;
            outline-color:invert;
            outline-style:none;
            outline-width:none;
            text-decoration:none;
        }
    </style>

    <title>Tela Topo</title>

    </head>

    <body topmargin="0" leftmargin="0" bgcolor="#D6D3CE">

        <!-- Inicio tabela topo-->
        <table cellpadding="0" cellspacing="0" bgcolor="#fff" width="100%" border="0">
            <tr class="barraTopo">
                <td class="barraTopo" colspan="3"></td>
            </tr>
            <tr>
                <!-- Logo Urbem -->
                <td width="175" bgcolor="#ffffff" height="55" align="center">
                    <img  style="padding: 0px 0px 5px 20px;" src=<?=CAM_FW_IMAGENS?>logo_topo.png width="175" height="54">
                </td>

                <!-- Nome Prefeitura -->
                <td background="<?=CAM_FW_IMAGENS?>bg_topo.png" height="55" align="right">
                    <font color="#4a6491" size="6" face="Futura Md BT, arial, courier"><strong><?=$exec_prefeitura;?></strong>&nbsp;</font>
                </td>

                <!-- Brasão Prefeitura -->
                <td width="60">
                    <img src="<?=CAM_FW_IMAGENS?><?=$exec_image;?>" width="60" height="55" border="0">
                </td>
            </tr>
        </table>

    </body>
</html>
