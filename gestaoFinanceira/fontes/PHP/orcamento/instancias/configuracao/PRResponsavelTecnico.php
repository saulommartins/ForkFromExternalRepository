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
    * Página de processamento de Responsável Técnico
    * Data de Criação   : 15/07/2004

    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 31899 $
    $Name$
    $Autor: $
    $Date: 2007-02-06 16:57:46 -0200 (Ter, 06 Fev 2007) $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"   );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                     );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ResponsavelTecnico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

/**
    * Instância o OBJETO da regra de negócios RResponsavelTecnico
*/
$obRResponsavel = new RCEMResponsavelTecnico;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {

    case "incluir":
        /**
            * Seta valores do Responsável Técnico a ser incluído
        */
        $obRResponsavel->setNumCgm          ( $_POST["inNumCGM"]          );
        $obRResponsavel->setCodigoProfissao ( $_POST["inCodigoProfissao"] );
        $obRResponsavel->setNumRegistro     ( $_POST["inCodigoRegistro"]  );
        $obRResponsavel->setCodigoUf        ( $_POST["inCodigoUF"]        );
        $obErro = new Erro;
        if ($_POST["inCodigoRegistro"] == 0 or $_POST["inCodigoRegistro"] == "") {
            $obErro->setDescricao("Número do registro inválido");
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GT_CEM_MAPEAMENTO."TCEMResponsavelTecnico.class.php");
            $obTCEMResponsavelTecnico = new TCEMResponsavelTecnico;
            $stFiltro = " WHERE num_registro = '".$_POST['inCodigoRegistro']."'";
            $obTCEMResponsavelTecnico->recuperaTodos($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                SistemaLegado::exibeAviso("Já existe um Responsável Técnico cadastrado com este código!","n_incluir","erro");
            } else {
                if ( !$obErro->ocorreu() ) {
                    $obErro = $obRResponsavel->incluirResponsavelTecnico();
                }
                if (!$obErro->ocorreu()) {
                    $obErro = $obRResponsavel->consultarResponsavelTecnico();
                    $obRCGM = new RCGM();
                    $obRCGM->setNumCGM( $obRResponsavel->getNumCgm());
                    $obRCGM->consultar($rsCGM);
                    SistemaLegado::alertaAviso($pgForm,"Responsável Técnico ".$obRCGM->getNomCGM(),"incluir","aviso", Sessao::getId(), "../");
                }
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro", Sessao::getId(), "../");
        }

    break;

    case "alterar":
        /**
            * Seta valores do Responsável Técnico a ser alterado
        */
        $obRResponsavel->setNumCgm          ( $_POST["inNumCGM"]          );
        $obRResponsavel->setSequencia       ( $_POST["inSequencia"]       );
        $obRResponsavel->setCodigoProfissao ( $_POST["inCodProfissao"] );
        $obRResponsavel->setNumRegistro     ( $_POST["inCodigoRegistro"]  );
        $obRResponsavel->setCodigoUf        ( $_POST["inCodigoUF"]        );

        $obErro = $obRResponsavel->alterarResponsavelTecnico();

        if (!$obErro->ocorreu()) {
            $obErro = $obRResponsavel->consultarResponsavelTecnico();
            $obRCGM = new RCGM();
            $obRCGM->setNumCGM( $obRResponsavel->getNumCgm());
            $obRCGM->consultar($rsCGM);
            SistemaLegado::alertaAviso($pgList."?stAcao=alterar&pg=".$_POST["pg"]."&pos=".$_POST["pos"],"Responsável Técnico ".$obRCGM->getNomCGM(),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro", Sessao::getId(), "../");
        }
    break;

    case "excluir";
        /**
            * Seta valores do Responsável Técnico a ser excluído
        */
        $obRResponsavel->setNumCgm          ( $_GET["inCodigoCGM"]       );
        $obRResponsavel->setCodigoProfissao ( $_GET["inCodigoProfissao"] );
        $obErro = $obRResponsavel->excluirResponsavelTecnico();
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&pg=".$_GET["pg"]."&pos=".$_GET["pos"],"Responsável Técnico ".$_GET["stDescQuestao"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&pg=".$_GET["pg"]."&pos=".$_GET["pos"],"Responsável Técnico não pode ser excluído porque está sendo utilizado.","n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
?>
