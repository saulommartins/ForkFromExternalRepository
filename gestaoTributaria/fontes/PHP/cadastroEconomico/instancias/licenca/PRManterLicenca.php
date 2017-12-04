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
 * Pagina de processamento para Licença
 * Data de Criação   : 02/12/2004
 * @author Analista: Ricardo Lopes
 * @author Desenvolvedor: Fábio Bertoldi Rodrigues
 * @package URBEM
 * @subpackage Regra

 $Id: PRManterLicenca.php 63390 2015-08-24 19:17:05Z arthur $

 * Casos de uso: uc-05.02.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMLicencaAtividade.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMLicencaEspecial.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php";
include_once CAM_GT_CEM_MAPEAMENTO."TCEMEmissaoDocumento.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma      = "ManterLicenca";
$pgFilt          = "FL".$stPrograma.".php";
$pgFiltAlterar   = "FLAlterarLicenca.php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormAtividade = "FMConcederLicencaAtividade.php";
$pgFormEspecial  = "FMConcederLicencaEspecial.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once $pgJs;

$obRCEMLicenca          = new RCEMLicenca;
$obRCEMLicencaAtividade = new RCEMLicencaAtividade;
$obRCEMLicencaEspecial  = new RCEMLicencaEspecial;

// função de comparação de data
function comparaDatasIgual($stData1,$stData2)
{
    list( $dia1,$mes1,$ano1 ) = explode( '/', $stData1 );
    list( $dia2,$mes2,$ano2 ) = explode( '/', $stData2 );
    if ("$ano1$mes1$dia1" >= "$ano2$mes2$dia2" )return true;
    else return false;
}

$obErro = new Erro;

switch ($request->get('stAcao')) {

    case "incAtiv":
        if ( $request->get("hdnNumeroLicenca") == 1 ) {

            $newLicenca = explode ( "/" , $request->get("inNumeroLicenca") );
            $exercicio_divida = $newLicenca[1];

            if ( !$exercicio_divida ) $exercicio_divida = Sessao::getExercicio();

            $obRCEMLicencaAtividade->setCodigoLicenca ( $newLicenca[0]    );
            $obRCEMLicencaAtividade->setExercicio     ( $exercicio_divida );

        } elseif ( $request->get("hdnNumeroLicenca") == 2 ) {

            $exercicio_divida = Sessao::getExercicio();
            $obRCEMLicencaAtividade->setExercicio  ( $exercicio_divida );

            $obRCEMLicenca->setExercicio( $exercicio_divida );
            $obRCEMLicenca->recuperaMaxCod( $rsNovaLicenca );

            $obRCEMLicencaAtividade->setCodigoLicenca ( $rsNovaLicenca->getCampo('max') + 1  );

        } else {

            $exercicio_divida = '0000';
            $obRCEMLicencaAtividade->setExercicio              ( $exercicio_divida );
            $obRCEMLicencaAtividade->obTCEMLicenca->proximoCod ( $maxCodigoLicenca );
            $obRCEMLicencaAtividade->setCodigoLicenca          ( $maxCodigoLicenca );
        }

        $dtDataInicio   	= $request->get("dtDataInicio");
        $dtDataTermino  	= $request->get("dtDataTermino");
        if ( $request->get('boEmissaoDocumento') == 'on' ) {
            $boEmissaoDocumento = true;
        }
        $dtDataInicioComp  = dt2int( $dtDataInicio  );
        $dtDataTerminoComp = dt2int( $dtDataTermino );
        if ($dtDataTermino) {
            if ( $dtDataInicioComp >= $dtDataTerminoComp)
                $obErro->setDescricao("Data de Término deve ser maior que a Data de Início");
        }

        $obRCEMLicencaAtividade->setDataInicio                 ( $dtDataInicio         );
        $obRCEMLicencaAtividade->setDataTermino                ( $dtDataTermino                    );
        $obRCEMLicencaAtividade->setObservacao				   ( $request->get('stObservacao')	   );
        $obRCEMLicencaAtividade->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $request->get("inInscricaoEconomica") );
        if ($request->get("inCodigoProcesso") != '') {
            $arProcesso = explode( "/" , $request->get("inCodigoProcesso") );
            $obRCEMLicencaAtividade->obRProcesso->setCodigoProcesso( $arProcesso[0]                    );
            $obRCEMLicencaAtividade->obRProcesso->setExercicio     ( $arProcesso[1]                    );
        }
        //verificar datas de inicio e termino

        if ( !$obErro->ocorreu() ) {
            $boTemPrincipal = false;
            $arAtividadeSessao = Sessao::read( "atividades" );
            if ( count ( $arAtividadeSessao ) < 1 ) {
                    $obErro->setDescricao("É necessário incluir uma atividade.");
            } else {
                foreach ($arAtividadeSessao as $arAtividade) {
                    $dtAtividadeInicio = dt2int($arAtividade["dt_inicio"]);
                    if ($arAtividade["principal"] == 't') {
                        $boTemPrincipal = true;
                    }
                    if ($dtDataInicioComp < $dtAtividadeInicio) {
                        $obErro->setDescricao("Data de Inicio da Licença deve ser maior ou igual a Data de Início da Atividade ".$arAtividade["cod_atividade"]."");
                    } else {
                        if ($arAtividade["dt_termino"]) { // se data do termino estiver preenchida, verifica
                        $dtAtividadeTermino = dt2int($arAtividade["dt_termino"]);
                            if ($dtDataTerminoComp > $dtAtividadeTermino) {
                                $obErro->setDescricao("Data de Término da Licença deve ser menor que a Data da Término Atividade ".$arAtividade["cod_atividade"]."");
                            }
                        }
                    }
                }//fim do FOR EACH
                if (!$boTemPrincipal) {
                    $obErro->setDescricao ("Para obter licença, é preciso que a atividade principal seja relacionada.");
                }
            }
        }

        if ( !$obErro->ocorreu() ) {

            $inCodTipoDocumento = $request->get('stCodDocumentoTxt');
            $stFiltro = "where a.cod_acao = ". Sessao::read('acao');
            $stFiltro .="AND b.cod_documento = ". $request->get('stCodDocumentoTxt');
            $obTModeloDocumento = new TAdministracaoModeloDocumento;
            $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

            while ( !$rsDocumentos->Eof() ) {
                $inCodTipoDocAtual 	= $rsDocumentos->getCampo( "cod_tipo_documento" );
                $inCodDocAtual		= $rsDocumentos->getCampo( "cod_documento" );
                $stNomeArquivo      	= $rsDocumentos->getCampo( "nome_arquivo_agt" );
                $stNomeDocumento    	= $rsDocumentos->getCampo( 'nome_documento' );
                $rsDocumentos->proximo();
            }

            $obRCEMLicencaAtividade->setAtividades( Sessao::read( "atividades" ));
            $obErro 		   = $obRCEMLicencaAtividade->concederLicenca();
            $codigoLicencaInclusao = $obRCEMLicencaAtividade->getCodigoLicenca();

            if ( !$obErro->ocorreu() ) {
                $obRCEMConfiguracao = new RCEMConfiguracao;
                $obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
                $obErro = $obRCEMConfiguracao->consultarConfiguracao();
                if ( !$obErro->ocorreu() ) {
                    #===================== INSERE NA TABELA EMISSAO DOCUMENTO
                    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDocumento.class.php");
                    $obTCEMLicencaDocumento = new TCEMLicencaDocumento;
                    $stFiltro = " WHERE ";
                    if ( $obRCEMConfiguracao->getNroAlvara() == "Exercicio" ) {
                        //por exercicio
                        $stFiltro .= " eld.exercicio = '".Sessao::getExercicio()."'";
                    } else {
                        $stFiltro .= " eld.exercicio = '".Sessao::getExercicio()."' AND eld.cod_documento = ".$request->get('stCodDocumento');//por documento
                    }

                    $obErro = $obTCEMLicencaDocumento->buscaUltimoNumeroAlvara( $rsEmissao, $stFiltro );
                    
                    if ( !$obErro->ocorreu() ) {
                        $inNumEmissao = ( $rsEmissao->getCampo('valor') + 1 );
                        if ( !$exercicio_divida )
                            $exercicio_divida = '0000';

                        $obTCEMLicencaDocumento->setDado( "cod_licenca", $codigoLicencaInclusao );
                        $obTCEMLicencaDocumento->setDado( "exercicio", $exercicio_divida );
                        $obTCEMLicencaDocumento->setDado( "num_alvara", $inNumEmissao );
                        $obTCEMLicencaDocumento->setDado( "cod_documento", $request->get('stCodDocumento') );
                        $obTCEMLicencaDocumento->setDado( "cod_tipo_documento", $request->get('inCodTipoDocumento') );
                        $obErro = $obTCEMLicencaDocumento->inclusao();
                    }
                }
            }

        if ( !$obErro->ocorreu() ) {

            if ($boEmissaoDocumento) {
                    $stCaminho = CAM_GT_CEM_INSTANCIAS."emissao/FMManterEmissao.php";

                    $stInscricoes = $stParametros = '';
                    $stParametros .= "&inNumeroLicenca=".$codigoLicencaInclusao;
                    $stParametros .= "&inExercicio=".$exercicio_divida;
                    $stParametros .= "&stTipoModalidade=emissao";
                    $stParametros .= "&stCodAcao=".Sessao::read('acao');
                    $stParametros .= "&stOrigemFormulario=conceder_licenca";

                    $stParametros .= "&inCodigoTipoDocumento=".$inCodTipoDocAtual;
                    $stParametros .= "&inCodigoDocumento=". $inCodDocAtual;
                    $stParametros .= "&stNomeArquivo=".$stNomeArquivo;
                    $stParametros .= "&stNomeDocumento=".$stNomeDocumento;
                    $stParametros .= "&inInscricaoEconomica=".$request->get("inInscricaoEconomica");
                    $stParametros .= "&stCtrl=Download";
                    sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Concessão de Licença", "incluir","aviso", Sessao::getId(), "../");
                } else {
                    sistemaLegado::alertaAviso( $pgFormAtividade."?stAcao=incAtiv","Código da Licença: ".$codigoLicencaInclusao,"incluir","aviso", Sessao::getId(), "../" );
                }
            } else {
                $obErro->setDescricao("Código da Licença já em uso");
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()." : ".$codigoLicencaInclusao),"n_incluir","erro");
        }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "incEsp":
        if ( $request->get('boEmissaoDocumento') == 'on' ) {
            $boEmissaoDocumento = true;
        }

        $obErro = new Erro;
        if ( $request->get("hdnNumeroLicenca") == 1 ) {

            $newLicenca = explode ( "/" , $request->get("inNumeroLicenca")?$request->get("inNumeroLicenca"):$request->get("inCodigoLicenca") );
            $exercicio_divida = $newLicenca[1];
            if ( !$exercicio_divida ) $exercicio_divida = Sessao::getExercicio();
            $obRCEMLicencaEspecial->setCodigoLicenca          ( $newLicenca[0]                    );
            $obRCEMLicencaEspecial->setExercicio              ( $exercicio_divida                 );

        } elseif ( $request->get("hdnNumeroLicenca") == 2 ) {
            $exercicio_divida = Sessao::getExercicio();
            $obRCEMLicencaEspecial->setExercicio              ( $exercicio_divida                 );

            $obRCEMLicenca->setExercicio( $exercicio_divida );
            $obRCEMLicenca->recuperaMaxCod( $rsNovaLicenca );

            $obRCEMLicencaEspecial->setCodigoLicenca       ( $rsNovaLicenca->getCampo('max')+1   );
            $codigoLicencaInclusao = $rsNovaLicenca->getCampo('max')+1;

        } else {
            $exercicio_divida = '0000';
            $obRCEMLicencaEspecial->setExercicio              ( $exercicio_divida      );
            $obRCEMLicencaEspecial->obTCEMLicenca->proximoCod ( $maxCodigoLicenca      );
            $obRCEMLicencaEspecial->setCodigoLicenca          ( $maxCodigoLicenca      );
            $codigoLicencaInclusao = $maxCodigoLicenca;

        }
        $dtDataInicio       = $request->get("dtDataInicio");
        $dtDataTermino      = $request->get("dtDataTermino");

        $dtDataInicioComp  = dt2int($dtDataInicio  );
        $dtDataTerminoComp = dt2int($dtDataTermino );
        if ( $dtDataInicioComp >= $dtDataTerminoComp)
            $obErro->setDescricao("Data de Término deve ser maior que a Data de Início");
        $obRCEMLicencaEspecial->setDataInicio                 ( $dtDataInicio );
        $obRCEMLicencaEspecial->setDataTermino                ( $dtDataTermino );
        $obRCEMLicencaEspecial->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $request->get("inInscricaoEconomica") );
        if ($request->get("inCodigoProcesso") != '') {
            $arProcesso = explode( "/" , $request->get("inCodigoProcesso") );
            $obRCEMLicencaEspecial->obRProcesso->setCodigoProcesso( $arProcesso[0]                    );
            $obRCEMLicencaEspecial->obRProcesso->setExercicio     ( $arProcesso[1]                    );
        }
        $obRCEMLicencaEspecial->setHorario                    ( Sessao::read( "horarios" )        );

        //verificar datas de inicio e termino
        if ( !$obErro->ocorreu() ) {
            foreach ( Sessao::read( "atividades" ) as $arAtividade ) {
                $dtAtividadeInicio = dt2int($arAtividade["dt_inicio"]);
                if ($dtDataInicioComp < $dtAtividadeInicio) {
                        $obErro->setDescricao("Data de Inicio da Licença deve ser maior ou igual a Data de Início da Atividade ".$arAtividade["cod_atividade"]."");
                } else {
                    if ($arAtividade["dt_termino"]) { // se data do termino estiver preenchida, verifica
                       $dtAtividadeTermino = dt2int($arAtividade["dt_termino"]);
                       if ($dtDataTerminoComp > $dtAtividadeTermino) {
                           $obErro->setDescricao("Data de Término da Licença deve ser menor que a Data da Término Atividade ".$arAtividade["cod_atividade"]."");
                        }
                    }
                }
            }
        }

        $obRCEMLicencaEspecial->setAtividades ( Sessao::read( "atividades" ) );

        if ( !$obErro->ocorreu() ) {
            $inCodTipoDocumento = $request->get('stCodDocumentoTxt');
            $stFiltro = "where a.cod_acao = ". Sessao::read('acao');
            $stFiltro .="AND b.cod_documento = ". $request->get('stCodDocumentoTxt');
            $obTModeloDocumento = new TAdministracaoModeloDocumento;
            $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

            while ( !$rsDocumentos->Eof() ) {
                $inCodTipoDocAtual  = $rsDocumentos->getCampo( "cod_tipo_documento" );
                $inCodDocAtual      = $rsDocumentos->getCampo( "cod_documento" );
                $stNomeArquivo      = $rsDocumentos->getCampo( "nome_arquivo_agt" );
                $stNomeDocumento    = $rsDocumentos->getCampo( 'nome_documento' );

                $rsDocumentos->proximo();
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMLicencaEspecial->concederLicenca( );

            if ( $request->get("hdnNumeroLicenca") == 1 ) {
                $codigoLicencaInclusao = $obRCEMLicencaEspecial->getCodigoLicenca();
            }

            if ( !$obErro->ocorreu() ) {
                $obRCEMConfiguracao = new RCEMConfiguracao;
                $obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
                $obErro = $obRCEMConfiguracao->consultarConfiguracao();
                if ( !$obErro->ocorreu() ) {
                    $stFiltro = " WHERE ";
                    if ( $obRCEMConfiguracao->getNroAlvara() == "Exercicio" ) {
                        //por exercicio
                        $stFiltro .= " eld.exercicio = '".Sessao::getExercicio()."'";
                    } else {
                        $stFiltro .= " eld.exercicio = '".Sessao::getExercicio()."' AND eld.cod_documento = ".$request->get('stCodDocumento');//por documento
                    }

                    #===================== INSERE NA TABELA EMISSAO DOCUMENTO
                    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDocumento.class.php");
                    $obTCEMLicencaDocumento = new TCEMLicencaDocumento;

                    Sessao::setTrataExcecao( true );
                    Sessao::getTransacao()->setMapeamento( $obTCEMLicencaDocumento );

                        $obTCEMLicencaDocumento->buscaUltimoNumeroAlvara( $rsEmissao, $stFiltro );
                        $inNumEmissao = ( $rsEmissao->getCampo('valor') + 1 );

                        if ( !$exercicio_divida )
                            $exercicio_divida = '0000';

                        $obTCEMLicencaDocumento->setDado( "cod_licenca", $codigoLicencaInclusao );
                        $obTCEMLicencaDocumento->setDado( "exercicio", $exercicio_divida );
                        $obTCEMLicencaDocumento->setDado( "num_alvara", $inNumEmissao );
                        $obTCEMLicencaDocumento->setDado( "cod_documento", $request->get('stCodDocumento') );
                        $obTCEMLicencaDocumento->setDado( "cod_tipo_documento", $request->get('inCodTipoDocumento') );

                        $obErro = $obTCEMLicencaDocumento->inclusao();

                    Sessao::encerraExcecao();
                }
            }

            if ( !$obErro->ocorreu() ) {
                if ($boEmissaoDocumento) {

                    $stCaminho = CAM_GT_CEM_INSTANCIAS."emissao/FMManterEmissao.php";

                    $stInscricoes = $stParametros = '';
                    $stParametros .= "&inNumeroLicenca=".$codigoLicencaInclusao;
                    $stParametros .= "&inExercicio=".$exercicio_divida;
                    $stParametros .= "&stTipoModalidade=emissao";
                    $stParametros .= "&stCodAcao=".Sessao::read('acao');
                    $stParametros .= "&stOrigemFormulario=conceder_licenca";
                    $stParametros .= "&stTipoLicenca=Licenca_Horario_Especial";

                    $stParametros .= "&inCodigoTipoDocumento=".$inCodTipoDocAtual;
                    $stParametros .= "&inCodigoDocumento=". $inCodDocAtual;
                    $stParametros .= "&stNomeArquivo=".$stNomeArquivo;
                    $stParametros .= "&stNomeDocumento=".$stNomeDocumento;
                    $stParametros .= "&inInscricaoEconomica=".$request->get("inInscricaoEconomica");
                    $stParametros .= "&stCtrl=Download";

                    sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Concessão de Licença", "incluir","aviso", Sessao::getId(), "../");

                } else {
                    sistemaLegado::alertaAviso($pgFormEspecial."?stAcao=incEsp","Código da Licença: ".$obRCEMLicencaEspecial->getCodigoLicenca(),"incluir","aviso", Sessao::getId(), "../");
                    Sessao::remove( "horarios");
                    Sessao::remove( "atividades");
                }
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
           sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "atividade":

        $stFiltro = "where a.cod_acao = ". Sessao::read('acao');
        $stFiltro .="AND b.cod_documento = ". $request->get('stCodDocumentoTxt');
        $obTModeloDocumento = new TAdministracaoModeloDocumento;
        $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

        while ( !$rsDocumentos->Eof() ) {
             $stNomeDocumento    = $rsDocumentos->getCampo( 'nome_documento' );
             $stNomeArquivo      = $rsDocumentos->getCampo( 'nome_arquivo_agt' );

             $rsDocumentos->proximo();
        }

        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
        $arProcesso = explode ( "/" , $request->get("inCodigoProcesso") );
        $obRCEMLicencaAtividade->setCodigoLicenca              ( $newLicenca[0]                    );
        $obRCEMLicencaAtividade->setExercicio                  ( $newLicenca[1]                    );
        $obRCEMLicencaAtividade->setDataInicio                 ( $request->get("dtDataInicio")         );
        $obRCEMLicencaAtividade->setDataTermino                ( $request->get("dtDataTermino")        );
        $obRCEMLicencaAtividade->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $request->get("inInscricaoEconomica") );
        $obRCEMLicencaAtividade->obRProcesso->setCodigoProcesso( $arProcesso[0]);
        $obRCEMLicencaAtividade->obRProcesso->setExercicio     ( $arProcesso[1]);

        $obRCEMLicencaAtividade->setAtividades                 ( Sessao::read( "atividades" )    );

        $obRCEMLicencaAtividade->setObservacao				   ( $request->get('stObservacao')		   );

        foreach ( Sessao::read( "atividades" ) as $arAtividade ) {
            if ($arAtividade["principal"] == 't') {
                $boTemPrincipal = true;
            }
        }

        if (!$boTemPrincipal) {
            $obErro->setDescricao ("Para obter licença, é preciso que a atividade principal seja relacionada.");
        } else {
            $obErro = $obRCEMLicencaAtividade->alterarLicenca();
        }

        if ( !$obErro->ocorreu() ) {

            if ( $request->get("boEmissaoDocumento") ) {        //boEmissaoDocumento

                $stCaminho = CAM_GT_CEM_INSTANCIAS."emissao/LSManterEmissao.php";
                $stInscricoes = $stParametros = '';
                $stParametros .= "&inCodLicenca=".$newLicenca[0];
                $stParametros .= "&inExercicio=".$newLicenca[1];
                $stParametros .= "&stTipoModalidade=alteracao";
                $stParametros .= "&stCodAcao=".Sessao::read('acao');
                $stParametros .= "&inOcorrenciaLicenca=".$obRCEMLicencaAtividade->getOcorrenciaLicenca();
                $stParametros .= "&stOrigemFormulario=alterar_licenca";
                $stParametros .= "&stAcao=atividade";
                $stParametros .= "&stTipoLicenca=2";
                $stParametros .= "&inInscricaoEconomica=".$request->get("inInscricaoEconomica");
                $stParametros .= "&inCodTipoDocumento2=".$request->get("inCodTipoDocumento");
                $stParametros .= "&stCodDocumentoTxt2=".$request->get("stCodDocumentoTxt");
                $stParametros .= "&stCodDocumento2=".$request->get("stCodDocumento");
                $stParametros .= "&stNomeDocumento=".$stNomeDocumento;
                $stParametros .= "&stNomeArquivo=".$stNomeArquivo;

                sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."","Conceder Licença", "alterar","aviso", Sessao::getId(), "../");

            } else {

                sistemaLegado::alertaAviso( $pgList."?stAcao=atividade&inInscricaoEconomica=".$request->get("inInscricaoEconomica")."","Código da Licença: ". $request->get("inCodigoLicenca"),"alterar","aviso", Sessao::getId(), "../" );

            }

        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "especial":

        $stFiltro = "where a.cod_acao = ". Sessao::read('acao');
        $stFiltro .="AND b.cod_documento = ". $request->get('stCodDocumentoTxt');
        $obTModeloDocumento = new TAdministracaoModeloDocumento;
        $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

        while ( !$rsDocumentos->Eof() ) {
             $stNomeDocumento    = $rsDocumentos->getCampo( 'nome_documento' );
             $stNomeArquivo      = $rsDocumentos->getCampo( 'nome_arquivo_agt' );

             $rsDocumentos->proximo();
         }

        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
        $obRCEMLicencaEspecial->setCodigoLicenca              ( $newLicenca[0]                    );
        $obRCEMLicencaEspecial->setExercicio                  ( $newLicenca[1]                    );
        $obRCEMLicencaEspecial->setDataInicio                 ( $request->get("dtDataInicio")     );
        $obRCEMLicencaEspecial->setDataTermino                ( $request->get("dtDataTermino")        );
        $obRCEMLicencaEspecial->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $request->get("inInscricaoEconomica") );
        $obRCEMLicencaEspecial->obRProcesso->setCodigoProcesso( $request->get("inCodigoProcesso")     );
        $obRCEMLicencaEspecial->obRProcesso->setExercicio     ( $request->get("hdnExercicioProcesso") );
        $obRCEMLicencaEspecial->setHorario                    ( Sessao::read( "horarios" )      );
        $obRCEMLicencaEspecial->setAtividades                 ( Sessao::read( "atividades" )    );
        $obErro = $obRCEMLicencaEspecial->alterarLicenca();
        if ( !$obErro->ocorreu() ) {

            if ( $request->get("boEmissaoDocumento") ) {

                $stCaminho = CAM_GT_CEM_INSTANCIAS."emissao/LSManterEmissao.php";
                $stInscricoes = $stParametros = '';
                $stParametros .= "&inCodLicenca=".$newLicenca[0];
                $stParametros .= "&inExercicio=".$newLicenca[1];
                $stParametros .= "&stTipoModalidade=alteracao";
                $stParametros .= "&stCodAcao=".Sessao::read('acao');
                $stParametros .= "&stOrigemFormulario=alterar_licenca";
                $stParametros .= "&stAcao=especial";
                $stParametros .= "&stTipoLicenca=2";
                $stParametros .= "&inInscricaoEconomica=".$request->get("inInscricaoEconomica");
                $stParametros .= "&inCodTipoDocumento2=".$request->get("inCodTipoDocumento");
                $stParametros .= "&stCodDocumentoTxt2=".$request->get("stCodDocumentoTxt");
                $stParametros .= "&stCodDocumento2=".$request->get("stCodDocumento");
                $stParametros .= "&stNomeDocumento=".$stNomeDocumento;
                $stParametros .= "&stNomeArquivo=".$stNomeArquivo;

                sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."","Conceder Licença", "alterar","aviso", Sessao::getId(), "../");

            } else {

                sistemaLegado::alertaAviso( $pgList."?stAcao=especial&inInscricaoEconomica=".$request->get("inInscricaoEconomica")."","Código da Licença: ". $request->get("inCodigoLicenca"),"alterar","aviso", Sessao::getId(), "../" );

            }

        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "baixar":
        $obErro = new Erro;
        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca")  );
        $arProcesso = explode ( "/" , $request->get("inCodigoProcesso") );
        $obRCEMLicenca->setCodigoLicenca              ( $newLicenca[0]                    );
        $obRCEMLicenca->setExercicio                  ( $newLicenca[1]                    );
        $obRCEMLicenca->setDataInicio                 ( $request->get("dtDataBaixa")          );
        $obRCEMLicenca->setMotivo                     ( $request->get("stMotivo")             );
        $obRCEMLicenca->obRProcesso->setCodigoProcesso( $arProcesso[0] );
        $obRCEMLicenca->obRProcesso->setExercicio     ( $arProcesso[1] );

        if ( $request->get("dtDataBaixa") ) {
            $obRCEMLicenca->setDataInicio            ( $request->get("dtDataBaixa")        );
            if ( SistemaLegado::comparaDatas($request->get("hdnDtDataConcessao"), $request->get("dtDataBaixa") )) {
                $obErro->setDescricao("Data de Baixa deve ser posterior a data da licença!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMLicenca->baixarLicenca();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=baixar","Código da Licença: ".$request->get("inCodigoLicenca"),"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;
    case "suspender":
        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
        $obErro = new Erro;
        if ( SistemaLegado::comparaDatas($request->get("hdnDtDataConcessao"),$request->get("dtDataSuspensao"))) {
            $obErro->setDescricao("Data da Suspensão deve ser maior que ".$request->get("hdnDtDataConcessao"));
        } else {
            $obRCEMLicenca->setCodigoLicenca              ( $newLicenca[0]                    );
            $obRCEMLicenca->setExercicio                  ( $newLicenca[1]                    );
            $obRCEMLicenca->setDataInicio                 ( $request->get("dtDataSuspensao")      );
            if ( $request->get("dtDataTermino") ) {
                $obRCEMLicenca->setDataTermino            ( $request->get("dtDataTermino")        );
                if ( SistemaLegado::comparaDatas($request->get("dtDataSuspensao") ,$request->get("dtDataTermino") )) {
                    $obErro->setDescricao("Data de Término deve ser posterior a data de Suspensão!");
                }
            }
            $obRCEMLicenca->setMotivo                     ( $request->get("stMotivo")             );
            $arProcesso = explode("/",$request->get("inCodigoProcesso") );
            $obRCEMLicenca->obRProcesso->setCodigoProcesso( $arProcesso[0]);
            $obRCEMLicenca->obRProcesso->setExercicio     ( $arProcesso[1]);
        }
        if ( !$obErro->ocorreu()) {
            $obErro = $obRCEMLicenca->suspenderLicenca();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=suspender","Código da Licença: ".$request->get("inCodigoLicenca"),"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;
    case "cancelar":
        $obErro     = new Erro;
        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
        $arProcesso = explode ( "/" , $request->get("inCodigoProcesso") );
        $obRCEMLicenca->setCodigoLicenca              ( $newLicenca[0]                      );
        $obRCEMLicenca->setExercicio                  ( $newLicenca[1]                      );
        $obRCEMLicenca->setDataInicio                 ( $request->get("hdnDtDataSuspensao")     );
        $obRCEMLicenca->setDataTermino                ( $request->get("dtDataTermino")          );
        $obRCEMLicenca->setMotivo                     ( $request->get("hdnStMotivo")            );
        $obRCEMLicenca->obRProcesso->setCodigoProcesso( $arProcesso[0]                      );
        $obRCEMLicenca->obRProcesso->setExercicio     ( $arProcesso[1]                      );
        if ( SistemaLegado::comparaDatas($obRCEMLicenca->getDataInicio(),$obRCEMLicenca->getDataTermino() )) {
            $obErro->setDescricao("Data de Término deve ser posterior a data de Suspensão!");
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMLicenca->cancelarSuspensao();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=cancelar","Código da Licença: ".$request->get("inCodigoLicenca"),"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;
    case "cassar":
        $obErro = new Erro;
        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
        $arProcesso = explode ( "/" , $request->get("inCodigoProcesso") );
        $obRCEMLicenca->setCodigoLicenca              ( $newLicenca[0]                    );
        $obRCEMLicenca->setExercicio                  ( $newLicenca[1]                    );
        $obRCEMLicenca->setMotivo                     ( $request->get("stMotivo")             );
        $obRCEMLicenca->obRProcesso->setCodigoProcesso( $arProcesso[0]  );
        $obRCEMLicenca->obRProcesso->setExercicio     ( $arProcesso[1]  );
        if ( $request->get("dtDataCassacao") ) {
            $obRCEMLicenca->setDataInicio            ( $request->get("dtDataCassacao")        );
            if ( SistemaLegado::comparaDatas($request->get("hdnDtDataConcessao"),$obRCEMLicenca->getDataInicio())) {
                $obErro->setDescricao("Data de Cassação deve ser posterior a data de Concessão(".$request->get("hdnDtDataConcessao").")!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMLicenca->cassarLicenca();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=cassar","Código da Licença: ".$request->get("inCodigoLicenca"),"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;
}

function dt2int($dtData)
{
    if ($dtData != '') {
       $tmp = explode("/",$dtData);
       $dtData = $tmp[2].$tmp[1].$tmp[0];
    }

    return $dtData;
}

?>
