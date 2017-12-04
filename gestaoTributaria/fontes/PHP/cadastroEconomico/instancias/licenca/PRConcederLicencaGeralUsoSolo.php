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
    * Pagina de processamento para Licenca Uso Solo
    * Data de Criação   : 09/04/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: André Machado
    * @package URBEM
    * @subpackage Regra

    * $Id: PRConcederLicencaGeralUsoSolo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicenca.class.php"                     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDiversa.class.php"              );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaObservacao.class.php"           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDocumento.class.php"            );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloArea.class.php"                 );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloEmpresa.class.php"              );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloImovel.class.php"               );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMUsoSoloLogradouro.class.php"           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoLicenca.class.php"             );

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoTipoLicencaDiversa.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoLicencaDiversaValor.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                  );#atributos dinamicos
include_once (CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ConcederLicencaGeralUsoSolo"  ;
$pgFilt        = "FL".$stPrograma.".php"        ;
$pgForm        = "FM".$stPrograma.".php"        ;
$pgProc        = "PR".$stPrograma.".php"        ;
$pgOcul        = "OC".$stPrograma.".php"        ;
$pgJs          = "JS".$stPrograma.".js"         ;

$obAtributos = new MontaAtributos;
$obAtributos->setName('AtributoLicenca_');
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {

    case "incGeral":
        $obTCEMLicenca = new TCEMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCEMLicenca );

        if ($_REQUEST["inCodigoLicenca"]) {
            $inCodigoLicenca = $_REQUEST["inCodigoLicenca"];
        } else {
            $obTCEMLicenca->proximoCod( $inCodigoLicenca, $boTransacao );
        }

        $obTCEMLicenca->setDado( "cod_licenca", $inCodigoLicenca           );
        $obTCEMLicenca->setDado( "exercicio"  , Sessao::getExercicio()         );
        $obTCEMLicenca->setDado( "dt_inicio"  , $_REQUEST["dtDataInicio"]  );
        $obTCEMLicenca->setDado( "dt_termino" , $_REQUEST["dtDataTermino"] );
        $obTCEMLicenca->inclusao();

        $obTCEMLicencaDiversa = new TCEMLicencaDiversa;
        $obTCEMLicencaDiversa->setDado( "cod_licenca", $inCodigoLicenca                 );
        $obTCEMLicencaDiversa->setDado( "exercicio"  , Sessao::getExercicio()               );
        $obTCEMLicencaDiversa->setDado( "cod_tipo"   , $_REQUEST["inCodigoTipoLicenca"] );
        $obTCEMLicencaDiversa->setDado( "numcgm"     , $_REQUEST["inNumCGM"]            );
        $obTCEMLicencaDiversa->inclusao();

        $obRCEMConfiguracao = new RCEMConfiguracao;
        $obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $obRCEMConfiguracao->consultarConfiguracao();
        $stFiltro = " WHERE ";
        if ( $obRCEMConfiguracao->getNroAlvara() == "Exercicio" ) {
            //por exercicio
            $stFiltro = " eld.exercicio = ".Sessao::getExercicio();
        } else {
            $stFiltro = " eld.exercicio = ".Sessao::getExercicio()." AND eld.cod_documento = ".$_REQUEST["inCodigoDocumento"];//por documento
        }

        $obTCEMLicencaDocumento = new TCEMLicencaDocumento;
        $obTCEMLicencaDocumento->BuscaUltimoNumeroAlvara( $rsAlvara, $stFiltro );
        $inNumAlvara = $rsAlvara->getCampo("valor") + 1;

        $obTCEMLicencaDocumento->setDado( "cod_licenca"        , $inCodigoLicenca                   );
        $obTCEMLicencaDocumento->setDado( "exercicio"          , Sessao::getExercicio()                 );
        $obTCEMLicencaDocumento->setDado( "cod_documento"      , $_REQUEST["inCodigoDocumento"]     );
        $obTCEMLicencaDocumento->setDado( "cod_tipo_documento" , $_REQUEST["inCodigoTipoDocumento"] );
        $obTCEMLicencaDocumento->setDado( "num_alvara"         , $inNumAlvara                       );
        $obTCEMLicencaDocumento->inclusao();

        if ($_REQUEST["stObservacao"]) {
            $obTCEMLicencaObservacao = new TCEMLicencaObservacao;
            $obTCEMLicencaObservacao->setDado( "cod_licenca", $inCodigoLicenca          );
            $obTCEMLicencaObservacao->setDado( "exercicio"  , Sessao::getExercicio()        );
            $obTCEMLicencaObservacao->setDado( "observacao" , $_REQUEST["stObservacao"] );
            $obTCEMLicencaObservacao->inclusao();
        }

        $obTCEMUsoSoloArea = new TCEMUsoSoloArea;
        $obTCEMUsoSoloArea->setDado( "cod_licenca", $inCodigoLicenca           );
        $obTCEMUsoSoloArea->setDado( "exercicio"  , Sessao::getExercicio()         );
        $obTCEMUsoSoloArea->setDado( "area"       , $_REQUEST["flAreaUnidade"] );
        $obTCEMUsoSoloArea->inclusao();

        $obTCEMUsoSoloEmpresa = new TCEMUsoSoloEmpresa;
        $obTCEMUsoSoloEmpresa->setDado( "cod_licenca"         , $inCodigoLicenca                  );
        $obTCEMUsoSoloEmpresa->setDado( "exercicio"           , Sessao::getExercicio()                );
        $obTCEMUsoSoloEmpresa->setDado( "inscricao_economica" , $_REQUEST["inInscricaoEconomica"] );
        $obTCEMUsoSoloEmpresa->inclusao();

        if ($_REQUEST["inCodImovel"]) {
            $obTCEMUsoSoloImovel = new TCEMUsoSoloImovel;
            $obTCEMUsoSoloImovel->setDado( "cod_licenca"         , $inCodigoLicenca         );
            $obTCEMUsoSoloImovel->setDado( "exercicio"           , Sessao::getExercicio()       );
            $obTCEMUsoSoloImovel->setDado( "inscricao_municipal" , $_REQUEST["inCodImovel"] );
            $obTCEMUsoSoloImovel->inclusao();
        }

        if ($_REQUEST["inNumLogradouro"]) {
            $obTCEMUsoSoloLogradouro = new TCEMUsoSoloLogradouro;
            $obTCEMUsoSoloLogradouro->setDado( "cod_licenca"    , $inCodigoLicenca             );
            $obTCEMUsoSoloLogradouro->setDado( "exercicio"      , Sessao::getExercicio()           );
            $obTCEMUsoSoloLogradouro->setDado( "cod_logradouro" , $_REQUEST["inNumLogradouro"] );
            $obTCEMUsoSoloLogradouro->inclusao();
        }

        if ($_REQUEST["inProcesso"]) {
            $arProcesso = explode ("/", $_REQUEST["inProcesso"]);

            $obTCEMProcessoLicenca = new TCEMProcessoLicenca;
            $obTCEMProcessoLicenca->setDado( "cod_licenca"        , $inCodigoLicenca   );
            $obTCEMProcessoLicenca->setDado( "exercicio"          , Sessao::getExercicio() );
            $obTCEMProcessoLicenca->setDado( "cod_processo"       , $arProcesso[0]     );
            $obTCEMProcessoLicenca->setDado( "exercicio_processo" , $arProcesso[1]     );
            $obTCEMProcessoLicenca->inclusao();
        }

        # Lançar Atributos dinâmicos
        $obRCadastroDinamico = new RCadastroDinamico;
        $obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa  );
        $obRCadastroDinamico->setPersistenteValores  ( new TCEMAtributoLicencaDiversaValor );
        $obRCadastroDinamico->setCodCadastro ( 4 );
        $obRCadastroDinamico->setChavePersistenteValores(
            array( "cod_licenca" => $inCodigoLicenca,
                   "exercicio"   => Sessao::getExercicio(),
                   "cod_tipo"    => $_REQUEST["inCodigoTipoLicenca"] )
         );

        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "AtributoLicenca_" );
        $obAtributos->recuperaVetor( $arChave );

        foreach ($arChave as $key=>$value) {
                 $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                 $inCodAtributo = $arChaves[0];
                 if ( is_array($value) ) {
                     $value = implode(",",$value);
                 }

                 $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
        }

        $obRCadastroDinamico->salvarValores();
        #-------------------------------

        Sessao::encerraExcecao();
        $stCaminho = CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeral.php";
        sistemaLegado::alertaAviso( $stCaminho."?"."&stAcao=incluir","Licença concedida com sucesso", "incluir","aviso", Sessao::getId(), "../");
  break;

    case "alterar":
        $inCodigoLicenca = Sessao::read( "inCodigoLicenca" );
        $obTCEMLicenca = new TCEMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCEMLicenca );

        $obTCEMLicenca->setDado( "cod_licenca", $inCodigoLicenca           );
        $obTCEMLicenca->setDado( "exercicio"  , Sessao::getExercicio()         );
        $obTCEMLicenca->setDado( "dt_inicio"  , $_REQUEST["dtDataInicio"]  );
        $obTCEMLicenca->setDado( "dt_termino" , $_REQUEST["dtDataTermino"] );
        $obTCEMLicenca->alteracao();

        if ($_REQUEST["stObservacao"]) {
            $obTCEMLicencaObservacao = new TCEMLicencaObservacao;
            $obTCEMLicencaObservacao->setDado( "cod_licenca", $inCodigoLicenca          );
            $obTCEMLicencaObservacao->setDado( "exercicio"  , Sessao::getExercicio()        );
            $obTCEMLicencaObservacao->setDado( "observacao" , $_REQUEST["stObservacao"] );

            $stFiltro = " WHERE cod_licenca = ".$inCodigoLicenca." AND exercicio = '".Sessao::getExercicio()."'";
            $obTCEMLicencaObservacao->recuperaTodos( $rsLicencaObservacao , $stFiltro );
            if ( !$rsLicencaObservacao->Eof() ) {
                $obTCEMLicencaObservacao->alteracao();
            } else {
                $obTCEMLicencaObservacao->inclusao();
            }
        }

        $obTCEMUsoSoloArea = new TCEMUsoSoloArea;
        $obTCEMUsoSoloArea->setDado( "cod_licenca", $inCodigoLicenca           );
        $obTCEMUsoSoloArea->setDado( "exercicio"  , Sessao::getExercicio()         );
        $obTCEMUsoSoloArea->setDado( "area"       , $_REQUEST["flAreaUnidade"] );
        $obTCEMUsoSoloArea->alteracao();

        $obTCEMUsoSoloEmpresa = new TCEMUsoSoloEmpresa;
        $obTCEMUsoSoloEmpresa->setDado( "cod_licenca"         , $inCodigoLicenca                  );
        $obTCEMUsoSoloEmpresa->setDado( "exercicio"           , Sessao::getExercicio()                );
        $obTCEMUsoSoloEmpresa->setDado( "inscricao_economica" , $_REQUEST["inInscricaoEconomica"] );
        $obTCEMUsoSoloEmpresa->alteracao();

        if ($_REQUEST["inCodImovel"]) {
            $obTCEMUsoSoloImovel = new TCEMUsoSoloImovel;
            $obTCEMUsoSoloImovel->setDado( "cod_licenca"         , $inCodigoLicenca         );
            $obTCEMUsoSoloImovel->setDado( "exercicio"           , Sessao::getExercicio()       );
            $obTCEMUsoSoloImovel->setDado( "inscricao_municipal" , $_REQUEST["inCodImovel"] );
            $obTCEMUsoSoloImovel->alteracao();
        }

        if ($_REQUEST["inNumLogradouro"]) {
            $obTCEMUsoSoloLogradouro = new TCEMUsoSoloLogradouro;
            $obTCEMUsoSoloLogradouro->setDado( "cod_licenca"    , $inCodigoLicenca             );
            $obTCEMUsoSoloLogradouro->setDado( "exercicio"      , Sessao::getExercicio()           );
            $obTCEMUsoSoloLogradouro->setDado( "cod_logradouro" , $_REQUEST["inNumLogradouro"] );
            $obTCEMUsoSoloLogradouro->alteracao();
        }

        if ($_REQUEST["inProcesso"]) {
            $arProcesso = explode ("/", $_REQUEST["inProcesso"]);

            $obTCEMProcessoLicenca = new TCEMProcessoLicenca;
            $obTCEMProcessoLicenca->setDado( "cod_licenca"        , $inCodigoLicenca   );
            $obTCEMProcessoLicenca->setDado( "exercicio"          , Sessao::getExercicio() );
            $obTCEMProcessoLicenca->setDado( "cod_processo"       , $arProcesso[0]     );
            $obTCEMProcessoLicenca->setDado( "exercicio_processo" , $arProcesso[1]     );

            $stFiltro = " WHERE cod_licenca = ".$inCodigoLicenca." AND exercicio = '".Sessao::getExercicio()."'";
            $obTCEMProcessoLicenca->recuperaTodos( $rsProcessoLicenca , $stFiltro);

            if ( !$rsProcessoLicenca->Eof() ) {
                $obTCEMProcessoLicenca->alteracao();
            } else {
                $obTCEMProcessoLicenca->inclusao();
            }
        }

        # Lançar Atributos dinâmicos
        $obRCadastroDinamico = new RCadastroDinamico;
        $obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa  );
        $obRCadastroDinamico->setPersistenteValores  ( new TCEMAtributoLicencaDiversaValor );
        $obRCadastroDinamico->setCodCadastro ( 4 );
        $obRCadastroDinamico->setChavePersistenteValores(
            array( "cod_licenca" => $inCodigoLicenca,
                   "exercicio"   => Sessao::getExercicio(),
                   "cod_tipo"    => $_REQUEST["inCodigoTipoLicenca"] )
         );

        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "AtributoLicenca_" );
        $obAtributos->recuperaVetor( $arChave );

        foreach ($arChave as $key=>$value) {
                 $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                 $inCodAtributo = $arChaves[0];
                 if ( is_array($value) ) {
                     $value = implode(",",$value);
                 }

                 $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
        }

        $obRCadastroDinamico->alterarValores();

        Sessao::encerraExcecao();
        $stCaminho = CAM_GT_ECONOMICO."instancias/licenca/FLAlterarLicencaGeral.php";
        sistemaLegado::alertaAviso( $stCaminho."?"."&stAcao=alterar","Licença alterada com sucesso", "alterar","aviso", Sessao::getId(), "../");
    break;

    case "excluir":

    break;

    default:
    include_once(CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeral.php");
}

?>
