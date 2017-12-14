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
    * Página de Processamento do Objeto
    * Data de Criação   : 04/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis
    * @ignore

    * Casos de uso: uc-03.04.07

*/

/*
$Log$
Revision 1.5  2007/03/13 15:52:28  hboaventura
Bug #8569#

Revision 1.4  2007/03/08 20:33:04  hboaventura
Correção de bug, alteração da mensagem para não gera problema no js

Revision 1.3  2007/02/23 20:40:22  bruce
Bug #8450#

Revision 1.2  2007/02/23 20:20:10  bruce
Bug #8184#
Bug #8262#

Revision 1.1  2007/02/09 17:16:34  hboaventura
Migração da Manutenção do Objeto para a Configuração

Revision 1.8  2007/02/07 17:00:24  bruce
apenas desfazendo uma alteração

Revision 1.7  2007/02/07 15:50:11  bruce
Bug #8184#

Revision 1.6  2007/01/23 18:34:34  bruce
Bug #8170#

Revision 1.5  2007/01/02 15:21:27  hboaventura
Bug #7911#

Revision 1.4  2006/11/13 20:17:17  rodrigo
Bug 7374,7378,7375

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ManterObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

Sessao::setTrataExcecao( true );

$obTComprasObjeto = new TComprasObjeto;
Sessao::getTransacao()->setMapeamento( $obTComprasObjeto );
switch ($stAcao) {

    case "incluir":
        $obTComprasObjeto->setDado( 'descricao',  $_REQUEST['stDescricao'] );
        $obTComprasObjeto->recuperaObjeto( $rsObjeto );
        if ( $rsObjeto->getNumLinhas() > 0 ) {
            SistemaLegado::exibeAviso(urlencode('Este objeto já está cadastrado'),"n_incluir","erro");
        } else {
            $obTComprasObjeto->inclusao();
            sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Objeto: ".$obTComprasObjeto->getDado('cod_objeto'),"incluir","aviso", Sessao::getId(), "../");
        }
    break;
    case "alterar":
        $obTComprasObjeto->setDado('cod_objeto', $_REQUEST['inCodigo']    );
        $obTComprasObjeto->setDado('descricao' , $_REQUEST['stDescricao'] );
        $obTComprasObjeto->alteracao();
        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Objeto: ".$obTComprasObjeto->getDado('cod_objeto'),"incluir","aviso", Sessao::getId(), "../");
    break;
    case "excluir":
        $boExcluir  = ( SistemaLegado::pegaDado( 'cod_objeto','compras.solicitacao',' WHERE cod_objeto = '.$_REQUEST['inCodigo'].' ' ) )   ? false : true;
        $boExcluir1 = ( SistemaLegado::pegaDado( 'cod_objeto','compras.mapa',' WHERE cod_objeto = '.$_REQUEST['inCodigo'].' ' ) )          ? false : true;
        $boExcluir2 = ( SistemaLegado::pegaDado( 'cod_objeto','compras.compra_direta',' WHERE cod_objeto = '.$_REQUEST['inCodigo'].' ' ) ) ? false : true;
        $boExcluir3 = ( SistemaLegado::pegaDado( 'cod_objeto','licitacao.licitacao',' WHERE cod_objeto = '.$_REQUEST['inCodigo'].' ' ) )   ? false : true;
        $boExcluir4 = ( SistemaLegado::pegaDado( 'cod_objeto','licitacao.convenio',' WHERE cod_objeto = '.$_REQUEST['inCodigo'].' ' ) )    ? false : true;

        if ($boExcluir && $boExcluir1 && $boExcluir2 && $boExcluir3 && $boExcluir4) {
            $obTComprasObjeto->setDado( 'cod_objeto', $_REQUEST['inCodigo'] );
            $obTComprasObjeto->exclusao();
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Objeto: ".$_REQUEST['inCodigo'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Este objeto está sendo utilizado","n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

Sessao::encerraExcecao();
?>
