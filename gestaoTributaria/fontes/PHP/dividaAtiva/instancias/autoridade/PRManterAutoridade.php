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
  * Página de Processamento da Autoridade
  * Data de criação : 14/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRManterAutoridade.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.04.08
**/

/*
$Log$
Revision 1.3  2007/03/01 13:21:45  cercato
Bug #8520#

Revision 1.2  2007/02/28 20:34:04  cercato
Bug #8518#

Revision 1.1  2006/09/18 17:18:29  cercato
formularios da autoridade de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATProcurador.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php" );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutoridade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];
$pgOcul = "OC".$stPrograma.".php";

switch ($_REQUEST['stAcao']) {
    case "incluir":
        if (!$_REQUEST["stTipoAutoridade"]) {
            SistemaLegado::exibeAviso( "O campo 'Tipo de Autoridade' deve ser preenchido.", "n_incluir", "erro" );
            exit;
        }

        if ($_REQUEST["stTipoAutoridade"] == "procurador") {
            if (!$_REQUEST["stOAB"]) {
                SistemaLegado::exibeAviso( "O campo 'OAB' deve ser preenchido.", "n_incluir", "erro" );
                exit;
            }

            if (!$_REQUEST["inCodUF"]) {
                SistemaLegado::exibeAviso( "O campo 'UF' deve ser preenchido.", "n_incluir", "erro" );
                exit;
            }
        }

        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " \nWHERE registro = ".$_REQUEST["inContrato"];
        $obTPessoalContrato->recuperaTodos( $rsContrato, $stFiltro );

        $inTamanhoArquivo = $_FILES['stCaminhoAssinatura']['size'];
        $fp = fopen($_FILES['stCaminhoAssinatura']['tmp_name'], "rb");
        $arquivo_temp = fread($fp, $inTamanhoArquivo);
        fclose($fp);

        $arCGM = explode("-", $_REQUEST["hdnCGM"]);
        $arCGM[0] = trim( $arCGM[0] );

        $obTDATAutoridade = new TDATAutoridade;
        $obTDATProcurador = new TDATProcurador;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATAutoridade );

            $obTDATAutoridade->proximoCod( $inCodAutoridade );

            $obTDATAutoridade->setDado( "cod_autoridade", $inCodAutoridade );
            $obTDATAutoridade->setDado( "cod_contrato", $rsContrato->getCampo("cod_contrato") );
            $obTDATAutoridade->setDado( "cod_norma", $_REQUEST["inCodNorma"] );
            $obTDATAutoridade->setDado( "numcgm", $arCGM[0] );
            $obTDATAutoridade->setDado( "tipo_assinatura", $_FILES['stCaminhoAssinatura']['type'] );
            $obTDATAutoridade->setDado( "tamanho_assinatura", $inTamanhoArquivo );
            $obTDATAutoridade->setDado( "assinatura", $arquivo_temp );

            $obTDATAutoridade->inclusao();
            if ($_REQUEST["stTipoAutoridade"] == "procurador") {
                $obTDATProcurador->setDado( "cod_autoridade", $inCodAutoridade );
                $obTDATProcurador->setDado( "oab", $_REQUEST["stOAB"] );
                $obTDATProcurador->setDado( "cod_uf", $_REQUEST["inCodUFTxt"] );

                $obTDATProcurador->inclusao();
            }

            sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Autoridade: ".$obTDATAutoridade->getDado('cod_autoridade'),"incluir","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
        break;

    case "alterar":
       if ($_REQUEST["stTipoAutoridade"] == "Procurador Municipal") {
            if (!$_REQUEST["stOAB"]) {
                SistemaLegado::exibeAviso( "O campo 'OAB' está vazio.", "n_incluir", "erro" );
                exit;
            }

            if (!$_REQUEST["inCodUF"]) {
                SistemaLegado::exibeAviso( "O campo 'UF' está vazio.", "n_incluir", "erro" );
                exit;
            }
        }

        $arquivo_temp = "";
        if ($_FILES['stCaminhoAssinatura']['name']) {
            $inTamanhoArquivo = $_FILES['stCaminhoAssinatura']['size'];
            $fp = fopen($_FILES['stCaminhoAssinatura']['tmp_name'], "rb");
            $arquivo_temp = fread($fp, $inTamanhoArquivo);
            fclose($fp);
        }

        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " \nWHERE registro = ".$_REQUEST["inMatricula"];
        $obTPessoalContrato->recuperaTodos( $rsContrato, $stFiltro );

        $obTDATAutoridade = new TDATAutoridade;
        $obTDATProcurador = new TDATProcurador;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATAutoridade );

            $obTDATAutoridade->setDado( "cod_autoridade", $_REQUEST["inCodAutoridade"] );
            $obTDATAutoridade->setDado( "cod_contrato", $rsContrato->getCampo("cod_contrato") );
            $obTDATAutoridade->setDado( "cod_norma", $_REQUEST["inCodNorma"] );
            $obTDATAutoridade->setDado( "numcgm", $_REQUEST["inCodCGM"] );
            if ($arquivo_temp) {
                $obTDATAutoridade->setDado( "tipo_assinatura", $_FILES['stCaminhoAssinatura']['type'] );
                $obTDATAutoridade->setDado( "tamanho_assinatura", $inTamanhoArquivo );
                $obTDATAutoridade->setDado( "assinatura", 0 );
            }

            $obTDATAutoridade->alteracao();
            if ($_REQUEST["stTipoAutoridade"] == "Procurador Municipal") {
                $obTDATProcurador->setDado( "cod_autoridade", $_REQUEST["inCodAutoridade"] );
                $obTDATProcurador->setDado( "oab", $_REQUEST["stOAB"] );
                $obTDATProcurador->setDado( "cod_uf", $_REQUEST["inCodUFTxt"] );

                $obTDATProcurador->alteracao();
            }

            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Autoridade: ".$obTDATAutoridade->getDado('cod_autoridade'),"alterar","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();

        break;

    case "excluir":
        $obTDATAutoridade = new TDATAutoridade;
        $obTDATProcurador = new TDATProcurador;
        $obTDATDividaAtiva = new TDATDividaAtiva;
        $stFiltro = " WHERE cod_autoridade = ".$_REQUEST["inCodAutoridade"]." LIMIT 1";
        $obTDATDividaAtiva->recuperaTodos( $rsListaDA, $stFiltro );
        if ( $rsListaDA->Eof() ) {
            Sessao::setTrataExcecao( true );
            Sessao::getTransacao()->setMapeamento( $obTDATAutoridade );

                if ($_REQUEST["stTipoAutoridade"] == "Procurador Municipal" || $_REQUEST['stTipo'] == 'Procurador Municipal') {
                    $obTDATProcurador->setDado( "cod_autoridade", $_REQUEST["inCodAutoridade"] );
                    $obTDATProcurador->exclusao();
                }

                $obTDATAutoridade->setDado( "cod_autoridade", $_REQUEST["inCodAutoridade"] );
                $obTDATAutoridade->exclusao();

                sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Autoridade: ".$obTDATAutoridade->getDado('cod_autoridade'),"excluir","aviso", Sessao::getId(), "../");

            Sessao::encerraExcecao();
        } else {
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Não é possível excluir Autoridade: ".$_REQUEST["inCodAutoridade"]."! Esta foi utilizada em inscrições da dívida ativa.","n_incluir","erro", Sessao::getId(), "../");
        }

        break;
}
