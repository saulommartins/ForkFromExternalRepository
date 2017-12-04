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
 * Página de Frame Oculto de Emissao
 * Data de Criação   : 16/10/2006

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Diego Bueno Coelho

 * @ignore

 $Id: OCManterEmissao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.02.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_CEM_MAPEAMENTO."TCEMEmissaoDocumento.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGM.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

//função para transformar as informações em ISO-8859-1(BR) para o arquivo tipo .odt
function TextoBr($entr)
{
    for ($i=0; $i<count($entr); $i++) {
        foreach ($entr[$i] as $key => $value) {
            $entr[$i][$key] = utf8_decode($entr[$i][$key]);
        }
    }

    return $entr;
}

switch ($request->get('stCtrl')) {
    case "Download":
        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php";
        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php";
        include_once CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php";
        include_once CAM_GT_CEM_MAPEAMENTO."TCEMLicenca.class.php";
        include_once CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeCadastroEconomico.class.php";
        include_once CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php";

        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obTMunicipio    = new TMunicipio;
        $obTUF           = new TUF;

        include_once CAM_OOPARSER."tbs_class.php";
        include_once CAM_OOPARSER."tbsooo_class.php";

        //PEGANDO INFORMACOES DA PREFEITURA
        $inArquivo = $_REQUEST["HdnQual"];

        if ($_REQUEST['stOrigemFormulario'] == 'consulta') {
            $inInscricaoEconomica   = $_REQUEST['inInscricaoEconomica'];
            $inCodLicenca           = $_REQUEST['inCodLicenca'];
            $inExercicio            = $_REQUEST['inExercicio'];
            $stNomeArquivoTemplate  = $_REQUEST['stNomeArquivoTemplate'];
            $stTipoLicenca          = 'Licenca '.$_REQUEST['stTipoLicenca'];
            $inCodDocumento         = $_REQUEST['inCodigoDocumento'];
            $inCodTipoDocumento     = $_REQUEST['inCodigoTipoDocumento'];
            $stNomDocumento         = $_REQUEST['stNomDocumento'];

        } else {
            $arDadosSessao = Sessao::read( "dados" );
            $inInscricaoEconomica   = $arDadosSessao[$inArquivo]['inInscricaoEconomica'];
            $inCodLicenca           = $arDadosSessao[$inArquivo]['inCodigoLicenca'];
            $inExercicio            = $arDadosSessao[$inArquivo]['inExercicio'];
            $stNomeArquivoTemplate  = $arDadosSessao[$inArquivo]['stNomeArquivoTemplate'];
            $stTipoLicenca          = $arDadosSessao[$inArquivo]['stTipoLicenca'];
            $inCodDocumento         = $arDadosSessao[$inArquivo]['inCodigoDocumento'];
            $inCodTipoDocumento     = $arDadosSessao[$inArquivo]['inCodigoTipoDocumento'];
            $stNomDocumento         = $arDadosSessao[$inArquivo]['stNomeDocumento'];
        }

        $stNomeArquivoDownload  = str_replace ( ' ', '_', $stNomDocumento );
        $stNomeArquivoDownload .= '_'.$inCodLicenca.'_'.$inExercicio.'.odt';

        $stChave =  $obTConfiguracao->getComplementoChave();
        $obTConfiguracao->setComplementoChave("parametro,cod_modulo");
        $arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "",
                                "e_mail" => "", "logradouro" => "", "numero" => "", "sigla_uf" => "",
                                "cep" => "" , "logotipo" => "", "cod_uf" => "", "nom_uf" => "",
                                "cod_municipio" => "", "nom_municipio" => "", "CGMPrefeito" => "" );
        $obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
        foreach ($arPropriedades as $stParametro => $stValor) {
            $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = '".$stParametro."'";
            $obErro = $obTConfiguracao->recuperaTodos( $rsDados, $stFiltro );
            $stValor = $rsDados->getCampo("valor");
            $arConfiguracao[0][$stParametro] = $stValor;
            if ( $obErro->ocorreu() ) {
                break;
            }
        }

        $obTConfiguracao->setComplementoChave( $stChave );
        $stFiltroMunicipio = " WHERE sw_municipio.cod_uf = ".$arConfiguracao[0]['cod_uf'];
        $stFiltroMunicipio .=" and sw_municipio.cod_municipio = ".$arConfiguracao[0]['cod_municipio'];
        $obTMunicipio->recuperaTodos( $rsMunicipio, $stFiltroMunicipio );

        $stFiltroUF = " WHERE sw_uf.cod_uf = ".$arConfiguracao[0]['cod_uf'];
        $obTUF->recuperaTodos( $rsUF, $stFiltroUF );

        $arConfiguracao[0]['nom_municipio'] = $rsMunicipio->getCampo('nom_municipio');
        $arConfiguracao[0]['nom_uf'] = $rsUF->getCampo('nom_uf');
        $arConfiguracao[0]['sigla_uf'] = $rsUF->getCampo('sigla_uf');

        $rsConfiguracao = new RecordSet;
        $rsConfiguracao->preenche ( $arConfiguracao );

        // CONSULTA CONFIGURACAO DO MODULO ECONOMICO
        $obRCEMConfiguracao = new RCEMConfiguracao;
        $obRCEMConfiguracao->setCodigoModulo( 14 );
        $obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $obRCEMConfiguracao->consultarConfiguracao();
        $stMascaraInscricao = $obRCEMConfiguracao->getMascaraInscricao();

        //===================== INSERE NA TABELA EMISSAO DOCUMENTO
        include_once( CAM_GT_CEM_MAPEAMENTO."TCEMEmissaoDocumento.class.php");
        $obTCEMEmissaoDocumento = new TCEMEmissaoDocumento;

        $stFiltro = " WHERE cod_licenca = ".$inCodLicenca;
        $obTCEMEmissaoDocumento->recuperaUltimoRegistro( $rsEmissao, $stFiltro );
        $inNumEmissao = ( $rsEmissao->getCampo('valor') + 1 );

        // DEVE-SE TER O VALOR CORRESPONDENTE AO DIRETOR TRIBUTOS NA CONFIGURACAO
        $obTConfiguracao->setDado ( 'cod_modulo', 14 );
        $obTConfiguracao->setDado ( 'parametro', 'diretor_tributos' );
        $obTConfiguracao->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTConfiguracao->recuperaPorChave ( $rsConfiguracao );
        $cgm_diretor_tributos = $rsConfiguracao->getCampo ('valor');

        if (!$inExercicio) {
            $inExercicio = '0000';
        }

        $obTCEMEmissaoDocumento->setDado( "cod_licenca"        , $inCodLicenca );
        $obTCEMEmissaoDocumento->setDado( "exercicio"          , $inExercicio );
        $obTCEMEmissaoDocumento->setDado( "num_emissao"        , $inNumEmissao );
        $obTCEMEmissaoDocumento->setDado( "cod_documento"      , $inCodDocumento );
        $obTCEMEmissaoDocumento->setDado( "cod_tipo_documento" , $inCodTipoDocumento );

        $obTCEMEmissaoDocumento->setDado ( "numcgm_usuario", Sessao::read('numCgm') );
        $obTCEMEmissaoDocumento->setDado ( "numcgm_diretor", $cgm_diretor_tributos );

        $obTCEMEmissaoDocumento->setDado ( "dt_emissao", date ("d/m/Y") );

        $timestamp = date('Y-m-d H:i:s');

        $obTCEMEmissaoDocumento->setDado ( "timestamp", $timestamp );
        $obTCEMEmissaoDocumento->inclusao();

        // RECUPERA OS DADOS DA INSCRICAO ECONOMICA
        $obTCEMLicenca = new TCEMLicenca;
        $rsLicenca = new RecordSet;
        $rsAtividades = new RecordSet;

        if (($stNomDocumento == "Alvará Sanitário - por atividade" ||
           $stNomDocumento == 'Alvará Funcionamento' ||
           $stNomDocumento == 'Alvará Autonomo' ||
           $stNomDocumento == 'alvara_atividade' ||
           $stNomDocumento == 'Alvará Atividade' ||
           $stNomDocumento == 'Alvará Vigilância Sanitária - Manaquiri' ||
           $stNomDocumento == 'Alvará de Funcionamento - Manaquiri' ||
           $stNomDocumento == 'Alvará Provisório - Manaquiri' ||
           $stNomDocumento == 'Alvará de Construção - Manaquiri') && $inCodTipoDocumento == 1 ) {

            $stFiltroLicenca = " WHERE usuario.numcgm = ".Sessao::read('numCgm')." \n";
            $stFiltroLicenca .=" AND ela.exercicio = '". $inExercicio ."' \n";
            $stFiltroLicenca .=" AND ela.inscricao_economica = ".$inInscricaoEconomica. " \n";
            $stFiltroLicenca .=" AND ela.cod_licenca = ".$inCodLicenca." \n";
            $stFiltroLicenca .=" AND ativide_principal.principal = true \n";

            $obTCEMLicenca->buscaDadosConcederLicencaAtividade ( $rsLicenca, Sessao::getExercicio(), $stFiltroLicenca );

        } elseif ( ( $stNomDocumento == 'alvara_horario_especial' ||  $stNomDocumento == 'Alvará Sanitário - horário especial' ||  $stNomDocumento == 'Alvará Horário Especial' )  && $inCodTipoDocumento == 1 ) {

            $stFiltroLicenca = " WHERE usuario.numcgm = ". Sessao::read('numCgm') ." \n";
            $stFiltroLicenca .=" AND ela.exercicio = '". $inExercicio ."' \n";
            $stFiltroLicenca .=" AND ela.inscricao_economica = ".$inInscricaoEconomica. " \n";
            $stFiltroLicenca .=" AND ela.cod_licenca = ".$inCodLicenca." \n";
            $obTCEMLicenca->buscaDadosConcederLicencaEspecial ( $rsLicenca, $stFiltroLicenca );
        } elseif ( ( $stNomDocumento == "Autorização Provisória" || $stNomDocumento == 'alvara_diversos' || $stNomDocumento == 'Alvará Diversos' || $stNomDocumento == 'Alvará Sanitário - diversos' ) && $inCodTipoDocumento == 1 ) {

            $boDiversa = true;

            $stFiltroLicenca = " WHERE usuario.numcgm = ". Sessao::read('numCgm') ." \n";
            $stFiltroLicenca .=" AND ela.exercicio = '". $inExercicio ."' \n";
            $stFiltroLicenca .=" AND ela.cod_licenca = ".$inCodLicenca." \n";

            $obTCEMLicenca->buscaDadosConcederLicencaDiversa ( $rsLicenca, Sessao::getExercicio(), $stFiltroLicenca );
        }

        if ( $rsLicenca->getCampo("cpf") != "" ) {
            $stCPFCNPJ = $rsLicenca->getCampo("cpf");
            $obMascara = new MascaraCPF;
            $obMascara->setDesmascarado( $stCPFCNPJ );
            $obMascara->mascaraDinamica();
            $stCPFCNPJ = $obMascara->getMascarado();
        } else {
            $stCPFCNPJ = $rsLicenca->getCampo("cnpj");
            if (isset($stCPFCNPJ)) {
                if ($stCPFCNPJ) {
                    $cnpj1 = substr( $stCPFCNPJ, 0, 2 );
                    $cnpj2 = substr( $stCPFCNPJ, 2, 3 );
                    $cnpj3 = substr( $stCPFCNPJ, 5, 3 );
                    $cnpj4 = substr( $stCPFCNPJ, 8, 4 );
                    $cnpj5 = substr( $stCPFCNPJ, 12, 2 );
                    $stCPFCNPJ = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
                }
            }
        }

        $rsLicenca->setCampo ('cnpj_cpf', $stCPFCNPJ );

        $stCNPJPrefeitura = $rsLicenca->getCampo("prefeitura_cnpj");

        if (isset($stCNPJPrefeitura)) {
            if ($stCNPJPrefeitura) {
                $cnpj1 = substr( $stCNPJPrefeitura, 0, 2 );
                $cnpj2 = substr( $stCNPJPrefeitura, 2, 3 );
                $cnpj3 = substr( $stCNPJPrefeitura, 5, 3 );
                $cnpj4 = substr( $stCNPJPrefeitura, 8, 4 );
                $cnpj5 = substr( $stCNPJPrefeitura, 12, 2 );
                $stCNPJPrefeitura = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
            }
        }

        $rsLicenca->setCampo('prefeitura_cnpj', $stCNPJPrefeitura );
        $rsLicenca->setCampo('num_emissao', $inNumEmissao );

        if (!$boDiversa) {

            $obTCEMAtividadeCadastroEconomico = new TCEMAtividadeCadastroEconomico;
            $stFiltroAtividade = " AND ATE.INSCRICAO_ECONOMICA = ".$inInscricaoEconomica." \n";
            $stFiltroAtividade .=" AND ATE.PRINCIPAL = false \n";					//Mudei aqui também de false para true
            $obTCEMAtividadeCadastroEconomico->recuperaAtividadeInscricao ( $rsAtividades, $stFiltroAtividade );

            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDiasSemana.class.php" );
            $obTCEMLicencaDiasSemana = new TCEMLicencaDiasSemana;

            $rsHorarios = new RecordSet;
            if ($inExercicio && $inCodLicenca) {
                $stFiltroHorarios = " AND exercicio = '".$inExercicio."'";
                $stFiltroHorarios .=" AND cod_licenca = ".$inCodLicenca;
                $obTCEMLicencaDiasSemana->recuperaRelacionamento( $rsHorarios, $stFiltroHorarios );
                if ( $rsHorarios->getNumLinhas() > 0 )
                    $rsHorarios->ordena("cod_dia");
            }
        } else {

            include_once CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php";
            include_once CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php";
            // instancia objeto para listagem de elementos
            $obRCEMElementos = new RCEMElemento( $obAtividadeTmp );

            $obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
            $obRCEMElementos->referenciaTipoLicencaDiversa( $obRCEMTipoLicencaDiversa );
            $obRCEMElementos->roRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $rsLicenca->getCampo('cod_tipo'));
            $obRCEMElementos->listarElementoTipoLicencaDiversa( $rsElementosLicencaDiversa );

            include_once CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php";
            $obRCEMLicencaDiversa   = new RCEMLicencaDiversa;

            $arElementosAtributo = array();
            while ( !$rsElementosLicencaDiversa->eof() ) {

                $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->setChavePersistenteValores(
                    array(
                        "cod_elemento"=> $rsElementosLicencaDiversa->getCampo('cod_elemento'),
                        "cod_licenca"=> $inCodLicenca
                    )
                );

                $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->recuperaAtributosSelecionadosValores ( $rsAtributosElementos );

                if ( $rsAtributosElementos->getCampo('valor') ) {
                    if ( $rsAtributosElementos->getCampo('nom_tipo') == 'Lista' ) {
                        $arValorPadrao = explode (',', $rsAtributosElementos->getCampo('valor_padrao') );
                        $inCount = 0;
                        while ( $inCount < count($arValorPadrao) ) {
                            if ( $arValorPadrao[$inCount] == $rsAtributosElementos->getCampo('valor') ) {
                                $inKey = $inCount;
                            }
                            $inCount++;
                        }

                        $arValorDescricao = explode('[][][]', $rsAtributosElementos->getCampo('valor_padrao_desc'));
                        $stValorDescricao = $arValorDescricao[$inKey];

                    } else {
                        $stValorDescricao = $rsAtributosElementos->getCampo('valor');
                    }

                    $arElementosAtributo[] =  array (
                        "cod_atributo" => $rsAtributosElementos->getCampo('cod_atributo'),
                        "nom_atributo" => $rsAtributosElementos->getCampo('nom_atributo'),
                        "valor" => $stValorDescricao
                    );
                }

                $rsElementosLicencaDiversa->proximo();
            }

            $rsElementosAtributos = new RecordSet;
            $rsElementosAtributos->preenche ( $arElementosAtributo );

            #======================================================================

            /* Atributos Dinamicos */
            $obRCEMLicencaDiversa = new RCEMLicencaDiversa;
            $obRCEMLicencaDiversa->obRCadastroDinamico->setChavePersistenteValores(
                array   (
                        "cod_tipo"      => $rsLicenca->getCampo('cod_tipo')    ,
                        "cod_licenca"   => $inCodLicenca
                        )
            );
            $obRCEMLicencaDiversa->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLicencaDiversa );

            $arElementosSelecionado = array();
            while ( !$rsAtributosLicencaDiversa->eof() ) {
                if ( $rsAtributosLicencaDiversa->getCampo('valor') ) {
                    if ( $rsAtributosLicencaDiversa->getCampo('nom_tipo') == 'Lista' ) {
                        $arValorPadrao = explode (',', $rsAtributosLicencaDiversa->getCampo('valor_padrao') );
                        $inCount = 0;
                        while ( $inCount < count($arValorPadrao) ) {
                            if ( $arValorPadrao[$inCount] == $rsAtributosLicencaDiversa->getCampo('valor') ) {
                                $inKey = $inCount;
                            }
                            $inCount++;
                        }

                        $arValorDescricao = explode('[][][]', $rsAtributosLicencaDiversa->getCampo('valor_padrao_desc'));
                        $stValorDescricao = $arValorDescricao[$inKey];

                    } else {
                        $stValorDescricao = $rsAtributosLicencaDiversa->getCampo('valor');
                    }

                    $arElementosSelecionado[] =  array (
                        "cod_atributo" => $rsAtributosLicencaDiversa->getCampo('cod_atributo'),
                        "nom_atributo" => $rsAtributosLicencaDiversa->getCampo('nom_atributo'),
                        "valor" => $stValorDescricao
                    );
                }

                $rsAtributosLicencaDiversa->proximo();
            }

            $rsElementosSelecionados = new RecordSet;
            $rsElementosSelecionados->preenche ( $arElementosSelecionado );
        }

    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoEmpresaDireitoValor.class.php" );
    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoLicenca.class.php" );
    $obTCEMAtributoEmpresaDireitoValor = new TCEMAtributoEmpresaDireitoValor;
    $obTCEMProcessoLicenca = new TCEMProcessoLicenca;

    if (!$boDiversa) {
        foreach ($rsLicenca->arElementos as $index => $key) {
            //necessário dividir a data de emissão para apresentar no arquivo do alvará
            $dataFrag = explode('/', $key['data_emissao']);
            switch ($dataFrag[1]) {
            case '01':
                $dataFrag[1] = "janeiro";
                break;
            case '02':
                $dataFrag[1] = "fevereiro";
                break;
            case '03':
                $dataFrag[1] = "março";
                break;
            case '04':
                $dataFrag[1] = "abril";
                break;
            case '05':
                $dataFrag[1] = "maio";
                break;
            case '06':
                $dataFrag[1] = "junho";
                break;
            case '07':
                $dataFrag[1] = "julho";
                break;
            case '08':
                $dataFrag[1] = "agosto";
                break;
            case '09':
                $dataFrag[1] = "setembro";
                break;
            case '10':
                $dataFrag[1] = "outubro";
                break;
            case '11':
                $dataFrag[1] = "novembro";
                break;
            case '12':
                $dataFrag[1] = "dezembro";
                break;
            }

            //divisão dos elementos do campo RUA
            $endereco = explode('-', $key['rua']);
            $rua = $endereco[0];
            $endereco = explode('Bairro: ', $endereco[1]);
            $numero = $endereco[0];
            $endereco = explode('CEP: ', $endereco[1]);
            $bairro = $endereco[0];

            //agora é recuperado os atributos, é necessário saber o tipo de inscrição econômica
            $obCadastroEconomico = new TCEMCadastroEconomico;
            $obCadastroEconomico->recuperaInscricao($rsTipoCadastro, ' AND ce.inscricao_economica = '.$inInscricaoEconomica.'');

            //ENQUADRAMENTO -> 1 = EMPRESA_FATO / 2 = EMPRESA_DIREITO / 3 = AUTONOMO

            if ($rsTipoCadastro->getCampo('enquadramento') == 1) {
            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoEmpresaFatoValor.class.php" );
            $obEmpresa = new TCEMAtributoEmpresaFatoValor;

            $obEmpresa->recuperaTodos ($rsEmpresa, ' WHERE inscricao_economica = '.$inInscricaoEconomica.'');
            } elseif ($rsTipoCadastro->getCampo('enquadramento') == 2) {
            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoEmpresaDireitoValor.class.php" );
            $obEmpresa = new TCEMAtributoEmpresaDireitoValor;

            $obEmpresa->recuperaTodos ($rsEmpresa, ' WHERE inscricao_economica = '.$inInscricaoEconomica.'');
            } else {
            include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoCadEconAutonomoValor.class.php" );
            $obEmpresa = new TCEMAtributoCadEconAutonomoValor;

            $obEmpresa->recuperaTodos ($rsEmpresa, ' WHERE inscricao_economica = '.$inInscricaoEconomica.'');
            }

            include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
            include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php" );

            $obAtributoDinamico = new TAdministracaoAtributoDinamico;
            $obAtributoValor = new TAdministracaoAtributoValorPadrao;

            $obAtributoDinamico->recuperaTodos($rsAtributoDinamico);

            foreach ($rsEmpresa->arElementos as $key1 => $value) {
                foreach ($rsAtributoDinamico->arElementos as $key2 => $value2) {
                    if ($value['cod_atributo'] == $value2['cod_atributo'] AND $value2['ativo'] == 't') {
                        if ($value2['nom_atributo'] == 'CE - Coeficiente de Edificação') {
                            $obAtributoValor->recuperaTodos($rsAtributoValor, ' WHERE cod_atributo = '.$value2['cod_atributo'].'
                                                    AND cod_modulo = '.$value['cod_modulo'].'
                                                    AND cod_valor = '.$value['valor'].'');
                            $rsLicenca->arElementos[$index]['edificacao'] = $rsAtributoValor->getCampo('valor_padrao');
                        } elseif ($value2['nom_atributo'] == 'KN - Coeficiente do Número de Empregados (CC - Coeficiente da Categoria)') {
                            $obAtributoValor->recuperaTodos($rsAtributoValor, ' WHERE cod_atributo = '.$value2['cod_atributo'].'
                                                    AND cod_modulo = '.$value['cod_modulo'].'
                                                    AND cod_valor = '.$value['valor'].'');
                            $rsLicenca->arElementos[$index]['categoria'] = $rsAtributoValor->getCampo('valor_padrao');
                        } elseif ($value2['nom_atributo'] == 'Alvara Isento?') {
                            $obAtributoValor->recuperaTodos($rsAtributoValor, ' WHERE cod_atributo = '.$value2['cod_atributo'].'
                                                    AND cod_modulo = '.$value['cod_modulo'].'
                                                    AND cod_valor = '.$value['valor'].'');
                            if ($rsAtributoValor->getCampo('valor_padrao') == 'Sim') {
                                $rsLicenca->arElementos[$index]['boIsencao'] = 'ISENTO';
                                $rsLicenca->arElementos[$index]['observacao'] = 'ESTE ALVARÁ DE LICENÇA DE FUNCIONAMENTO ESTÁ ISENTO DO PAGAMENTO DE TAXA.';
                            } else {
                                $rsLicenca->arElementos[$index]['boIsencao'] = 'NÃO ISENTO';
                                $rsLicenca->arElementos[$index]['observacao'] = 'ESTE ALVARÁ DE LICENÇA DE FUNCIONAMENTO DEVERÁ ESTAR ACOMPANHADO DO DAM - DOCUMENTO DE ARRECADAÇÃO MUNICIPAL, COM AUTENTICAÇÃO BANCÁRIA DO PAGAMENTO.';
                            }
                        }
                    }
                }
            }

            if (trim($numero) == '' || trim($numero) == '0') {
                $numero = 's/nº';
            }

            $obTCEMProcessoLicenca->recuperaTodos($rsProcesso, " WHERE cod_licenca = ".$inCodLicenca." AND exercicio = '".Sessao::getExercicio()."'");

            if (strlen((string) $rsProcesso->getCampo('cod_processo')) < 5) {
                $rsLicenca->arElementos[$index]['num_processo'] = str_pad((string) $rsProcesso->getCampo('cod_processo'), 5, "0", STR_PAD_LEFT);
            } else {
                $rsLicenca->arElementos[$index]['num_processo'] = (string) $rsProcesso->getCampo('cod_processo');
            }

            $rsLicenca->arElementos[$index]['num_processo'] .= "/".$rsProcesso->getCampo('exercicio_processo');
            $rsLicenca->arElementos[$index]['dt_emissao_dia'] = $dataFrag[0];
            $rsLicenca->arElementos[$index]['dt_emissao_mes'] = $dataFrag[1];
            $rsLicenca->arElementos[$index]['rua_frag'] = rtrim($rua);
            $rsLicenca->arElementos[$index]['rua_numero'] = trim($numero);
            $rsLicenca->arElementos[$index]['bairro'] = rtrim($bairro);

            if ($rsLicenca->arElementos[$index]['nom_fantasia'] == '') {
                $rsLicenca->arElementos[$index]['nom_fantasia'] = '********';
            }

            $dia = array ('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo');

            for ($iDia = 0; $iDia <= 7; $iDia++) {
                if ($key[''.$dia[$iDia].'_inicio'] != '') {
                    $rsLicenca->arElementos[$index]['hora_data_ini'] = $dia[$iDia];
                    $rsLicenca->arElementos[$index]['hora_ini'] = $key[''.$dia[$iDia].'_inicio'];
                    $rsLicenca->arElementos[$index]['hora_term'] = $key[''.$dia[$iDia].'_termino'];

                    for ($iDia2 = 1 + $iDia; $iDia2 <= 7; $iDia2++) {
                    if ($key[''.$dia[$iDia2].'_inicio'] != '') {
                        $rsLicenca->arElementos[$index]['hora_data_term'] = $dia[$iDia2];
                    }
                    }
                break;
            }
            }
        }
    }

    // instantiate a TBS OOo class
    $OOParser = new clsTinyButStrongOOo;

    // setting the object
    $OOParser->SetZipBinary('zip');
    $OOParser->SetUnzipBinary('unzip');
    $OOParser->SetProcessDir('/tmp');

    $stDocumentoPath = '/tmp/';
    $OOParser->_process_path = $stDocumentoPath; //nome do arquivo pra salva

    // create a new openoffice document from the template with an unique id
    $stArquivo = CAM_GT_CEM_ANEXOS ."alvara/".$stNomeArquivoTemplate;

    $OOParser->NewDocFromTpl( $stArquivo );
    $OOParser->LoadXmlFromDoc('content.xml');

    $cnpjPrefeitura = sistemalegado::pegaConfiguracao( 'cnpj',2 );
    $stFiltro = " AND cadastro_economico.inscricao_economica = ".$inInscricaoEconomica." \n";

    if ($cnpjPrefeitura == '04641551000195') { //manaquiri: Feito assim pois a inscricao municipal eh um atributo
        $obTCEMLicenca->buscaAtributoInscricaoMunicipalLicensaManaquiri( $rsAtributoInscricaoMunicipal, $stFiltro, $stOrder );
    } else {
        $rsAtributoInscricaoMunicipal = new Recordset;
    }

    $arAtividades = Sessao::read( "atividades" );

    $count=0;
    $principal=false;
    for ($i = 0; $i< count($arAtividades); $i++) {
        if ($arAtividades[$i]['principal_sn']=='n') {
            $rsAtividadesSecundarias[$count] = $arAtividades[$i];
            $count++;
        } elseif ($arAtividades[$i]['principal_sn']=='s') {
            $rsLicenca->arElementos[0]['cod_atividade'] = $arAtividades[$i]['cod_atividade'];
            $rsLicenca->arElementos[0]['nom_atividade'] = $arAtividades[$i]['nom_atividade'];
            $principal=true;
        }
    }
    if ($principal==false) {
        $rsLicenca->arElementos[0]['cod_atividade']='';
        $rsLicenca->arElementos[0]['nom_atividade']='';
    }

    $OOParser->MergeBlock( 'Conf', $rsConfiguracao->arElementos );
    $OOParser->MergeBlock( 'IE',   TextoBr($rsLicenca->arElementos) );
    $OOParser->MergeBlock( 'IM',   $rsAtributoInscricaoMunicipal->arElementos );

    if (!$boDiversa) {
        if (isset($rsAtividadesSecundarias)) {
            $OOParser->MergeBlock( 'Ati', TextoBr($rsAtividadesSecundarias) );
        } else {
            $atividade[0]['cod_atividade'] = isset($atividade[0]['cod_atividade']) ? $atividade[0]['cod_atividade'] : "";
            $atividade[0]['nom_atividade'] = isset($atividade[0]['nom_atividade']) ? $atividade[0]['nom_atividade'] : "";
            $atividade[0]['dt_inicio'] = isset($atividade[0]['dt_inicio']) ? $atividade[0]['dt_inicio'] : "";
            $OOParser->MergeBlock( 'Ati', $atividade );
        }

        if ( !$rsHorarios->Eof() ) {
            $OOParser->MergeBlock( 'Hor', $rsHorarios->arElementos );
        }
    } else {
        $OOParser->MergeBlock ('AtL', $rsElementosAtributos->arElementos );
        $OOParser->MergeBlock ('AtE', $rsElementosSelecionados->arElementos );
    }

    if ( ( $stNomDocumento == 'Alvará Sanitário - horário especial' ) || ( $stNomDocumento == 'Alvará Sanitário - diversos' ) ||
         ( $stNomDocumento == "Alvará Sanitário - por atividade" ) || ( $stNomDocumento == "Alvará Sanitário" ) ) {
        $obTCEMLicenca->buscaDadosDocumentoSanitario( $rsDadosSanitario );
        $OOParser->MergeBlock ('Sanit', TextoBr($rsDadosSanitario->arElementos) );
    }

    $OOParser->SaveXmlToDoc();
    $OOParser->LoadXmlFromDoc('styles.xml');
    $OOParser->SaveXmlToDoc();

    $typefile = 'application/sxw';
    header("Content-type: $typefile");
    header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
    header("Content-Disposition: attachment; filename=\"$stNomeArquivoDownload\"");
    readfile($OOParser->GetPathnameDoc());

    break;
}
