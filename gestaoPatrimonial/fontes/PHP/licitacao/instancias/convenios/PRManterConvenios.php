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
    * Formulario de Convenio
    * Data de Criação   : 03/10/2006

    * @author Analista:
    * @author Desenvolvedor:  Lucas Teixeira Stephanou
    * @ignore

    $Id: PRManterConvenios.php 63316 2015-08-17 14:21:56Z jean $

    *Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipanteConvenio.class.php" );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoConvenio.class.php" );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoConvenio.class.php" );

$stAcao = $request->get("stAcao");

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenios";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = 'OCGeraDocumentoConvenio.php';

$arValores = Sessao::read('arValores');

switch ($stAcao) {
    case 'incluir' :
        // validar participantes e veiculos de comunicação
        $arValores       = Sessao::read('arValores');
        $rsParticipantes = Sessao::read('participantes');

        if ( count($rsParticipantes) < 1 ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Participante.", "n_incluir", "erro" );
            exit;
        }

        if ( count($arValores) < 1 ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Veículo de Publicidade.", "n_incluir", "erro" );
            exit;
        }

        // preparar datas
        $dtAssinatura = $_REQUEST[ 'dtAssinatura' ];
        $dtFinalVigencia = $_REQUEST[ 'dtFinalVigencia' ];
        $dtInicioExecucao = $_REQUEST[ 'dtInicioExecucao' ];

        // valor

        $nuValorConvenio = str_replace ( ',' , '.' , str_replace ( '.' , '' , $_REQUEST[ 'nuValorConvenio' ] ) );

        $somaValorParticipantes = 0;
        foreach ($rsParticipantes->arElementos as $arParticipante) {
            $somaValorParticipantes = $somaValorParticipantes + $arParticipante['nuValorParticipacao'];
        }

        if (($somaValorParticipantes > $nuValorConvenio)||
            ($somaValorParticipantes < $nuValorConvenio)){
            SistemaLegado::exibeAviso("A soma do Valor de Participações deve ser igual a 100% do Valor do Convênio.", "n_incluir", "erro" );
            exit;
        }

        $obConvenio = new TLicitacaoConvenio;

        /// verificnado se ja existe o número do convenio
        $obConvenio->setDado( "num_convenio"        , $_REQUEST[ 'inNumConvenio' ] );
        $obConvenio->setDado( "exercicio"           , Sessao::getExercicio() );
        $obConvenio->recuperaPorChave ( $rsConvenio );

        if ( $rsConvenio->getNumLinhas() > 0 ) {
            SistemaLegado::exibeAviso( "Já existe um convênio cadastrado com este número.", "n_incluir", "erro" );
            exit;
        }

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obConvenio );
        // convenio
        $obConvenio->setDado( "num_convenio"          , $_REQUEST[ 'inNumConvenio' ] );
        $obConvenio->setDado( "exercicio"             , Sessao::getExercicio() );
        $obConvenio->setDado( "cgm_responsavel"       , $_REQUEST [ 'inCgmResponsavelJuridico' ] );
        $obConvenio->setDado( "cod_objeto"            , $_REQUEST [ 'stObjeto' ] );
        $obConvenio->setDado( "cod_tipo_convenio"     , $_REQUEST [ 'inCodTipoConvenio'] );
                                                      
        $obConvenio->setDado( "cod_tipo_documento"    , 0);
        $obConvenio->setDado( "cod_documento"         , 0);
        $obConvenio->setDado( "cod_tipo_documento"    , 0 );
        $obConvenio->setDado( "cod_documento"         , 0 );
        $obConvenio->setDado( "observacao"            , $_REQUEST [ 'stObservacao' ] );
        $obConvenio->setDado( "dt_assinatura"         , $dtAssinatura );
        $obConvenio->setDado( "dt_vigencia"           , $dtFinalVigencia );
        $obConvenio->setDado( "valor"                 , $nuValorConvenio);
        $obConvenio->setDado( "fundamentacao"         , $_REQUEST [ "stFundamentacaoLegal" ]);
        $obConvenio->setDado( "cod_norma_autorizativa", $_REQUEST [ "inCodLei" ]);
        $obConvenio->setDado( "cod_uf_tipo_convenio"  , $_REQUEST [ "inCodUF" ]);
        $obConvenio->setDado( "inicio_execucao"       , $dtInicioExecucao);
        $obConvenio->inclusao();
        $obTLicitacaoPublicacaoConvenio = new TLicitacaoPublicacaoConvenio();

        //inclui os dados da publicacao do convênio
        foreach ($arValores as $arTemp) {
            $obTLicitacaoPublicacaoConvenio->setDado( 'num_convenio'    , $obConvenio->getDado('num_convenio') );
            $obTLicitacaoPublicacaoConvenio->setDado( 'numcgm'          , $arTemp['inVeiculo'] );
            $obTLicitacaoPublicacaoConvenio->setDado( 'dt_publicacao'   , $arTemp['dtDataPublicacao'] );
            $obTLicitacaoPublicacaoConvenio->setDado( 'num_publicacao'  , $arTemp['inNumPublicacao'] );
            $obTLicitacaoPublicacaoConvenio->setDado( 'exercicio'       , Sessao::getExercicio() );
            $obTLicitacaoPublicacaoConvenio->setDado( 'observacao'      , $arTemp['_stObservacao'] );
            $obTLicitacaoPublicacaoConvenio->inclusao();
        }

        $obParConvenio = new TLicitacaoParticipanteConvenio;
        $rsParticipantes->setPrimeiroElemento();
        $inSomaValorParticipantes = 0;
        while ( !$rsParticipantes->eof() ) {
            // buscar dados do participante
            require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoParticipanteCertificacao.class.php");
            $obLicParCert = new TLicitacaoParticipanteCertificacao;
            $stFiltro = " WHERE cgm_fornecedor = ".$rsParticipantes->getCampo('inCgmParticipante')." ";
            $obLicParCert->recuperaRelacionamento ( $rsPar , $stFiltro , '' ) ;
            // valor
            $obParConvenio->setDado ( 'exercicio' , Sessao::getExercicio());
            $obParConvenio->setDado ( 'num_convenio' , $_REQUEST['inNumConvenio']);
            $obParConvenio->setDado ( 'num_certificacao' , $rsPar->getCampo ( 'num_certificacao' )  );
            $obParConvenio->setDado ( 'exercicio_certificacao' , $rsPar->getCampo( 'exercicio' ) );
            $obParConvenio->setDado ( 'cgm_fornecedor' , $rsPar->getCampo( 'cgm_fornecedor' ) );
            $obParConvenio->setDado ( 'cod_tipo_participante' , $rsParticipantes->getCampo ( 'inCodTipoParticipante' ) );
            $obParConvenio->setDado ( 'valor_participacao' , $rsParticipantes->getCampo ( 'nuValorParticipacao' ) );
            $obParConvenio->setDado ( 'percentual_participacao' , $rsParticipantes->getCampo ( 'hdnPercentualParticipacao' ) );
            $obParConvenio->setDado ( 'funcao'                  , $rsParticipantes->getCampo ( 'stFuncaoParticipante' ) );
            $obParConvenio->inclusao();

            $inSomaValorParticipantes = $inSomaValorParticipantes + $rsParticipantes->getCampo('nuValorParticipacao');
            $rsParticipantes->proximo();
        }

        Sessao::write('arRequest',$_REQUEST);

        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Convênio: ".$_REQUEST['inNumConvenio'],"incluir","aviso", Sessao::getId(), "../");

        SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId() );

        Sessao::encerraExcecao();

    break;

    case 'excluirConvenio':
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obConvenio );

        $obConvenio = new TLicitacaoConvenio;

        $inNumConvenio = $_REQUEST[ 'inNumConvenio' ];
        $stFiltro = ' convenio.num_convenio = ' . $inNumConvenio . ' AND' ;
        if ( $stFiltro )
             $stFiltro = " AND ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        $obConvenio->recuperaRelacionamento ( $rsConvenio , $stFiltro , ' convenio.num_convenio');

        $obParConvenio = new TLicitacaoParticipanteConvenio;
        $obParConvenio->setDado ( 'num_convenio' , $inNumConvenio);
        $obParConvenio->exclusao();

        $obPubConvenio = new TLicitacaoPublicacaoConvenio;
        $obPubConvenio->setDado ( 'num_convenio' , $inNumConvenio);
        $obPubConvenio->exclusao();

        $obConvenio->setDado ( 'num_convenio' , $inNumConvenio );
        $obConvenio->exclusao();

        sistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=excluir","Convênio: ".$_REQUEST['inNumConvenio'],"incluir","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
    break;

    case 'alterar':

        $inNumConvenio = $_REQUEST[ 'inNumConvenio' ];
        $rsConvenio    = new RecordSet();
        $obConvenio    = new TLicitacaoConvenio;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obConvenio );

        $obConvenio->setDado( 'num_convenio', $inNumConvenio     );
        $obConvenio->setDado( 'exericicio'  , Sessao::getExercicio() );
        $obConvenio->recuperaPorChave($rsConvenio);
        $obConvenio->setDado( "cgm_responsavel"       , $_REQUEST [ 'inCgmResponsavelJuridico' ] );
        $obConvenio->setDado( "observacao"            , $_REQUEST [ 'stObservacao' ]             );
        $obConvenio->setDado( "fundamentacao"         , $_REQUEST [ "stFundamentacaoLegal" ]);
        $obConvenio->setDado( "cod_norma_autorizativa", $_REQUEST [ "inCodLei" ]);
        $obConvenio->setDado( "cod_uf_tipo_convenio"  , $_REQUEST [ "inCodUF" ]);
        $obConvenio->alteracao();

        // excluir  participantes e veiculos

        $obParConvenio = new TLicitacaoParticipanteConvenio;
        $obParConvenio->setDado ( 'num_convenio' , $inNumConvenio);
        $obParConvenio->exclusao();

        $obPubConvenio = new TLicitacaoPublicacaoConvenio;
        $obPubConvenio->setDado ( 'num_convenio' , $inNumConvenio );
        $obPubConvenio->exclusao();

        // inserir novos
        $arValores       = Sessao::read('arValores');
        $rsParticipantes = Sessao::read('participantes');

        $obTLicitacaoPublicacaoConvenio = new TLicitacaoPublicacaoConvenio();
        $obTLicitacaoPublicacaoConvenio->setDado( 'num_convenio' , $_REQUEST['inNumConvenio']);
        $obTLicitacaoPublicacaoConvenio->setDado( 'exercicio'    , Sessao::getExercicio());
        $obTLicitacaoPublicacaoConvenio->exclusao();

        //inclui os dados da publicacao do contrato
        foreach ($arValores as $arTemp) {
            $obTLicitacaoPublicacaoConvenio->setDado( 'num_convenio'    , $obConvenio->getDado('num_convenio') );
            $obTLicitacaoPublicacaoConvenio->setDado( 'numcgm'          , $arTemp['inVeiculo'] );
            $obTLicitacaoPublicacaoConvenio->setDado( 'dt_publicacao'   , $arTemp['dtDataPublicacao'] );
            $obTLicitacaoPublicacaoConvenio->setDado( 'num_publicacao'  , $arTemp['inNumPublicacao'] );
            $obTLicitacaoPublicacaoConvenio->setDado( 'exercicio'       , Sessao::getExercicio() );
            $obTLicitacaoPublicacaoConvenio->setDado( 'observacao'      , $arTemp['_stObservacao'] );
            $obTLicitacaoPublicacaoConvenio->inclusao();
        }

        $rsParticipantes->setPrimeiroElemento();
        // buscar dados do participante
        require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoParticipanteCertificacao.class.php");
        $obLicParCert = new TLicitacaoParticipanteCertificacao;

        while ( !$rsParticipantes->eof() ) {
          // buscar dados do participante
          $stFiltro=" WHERE cgm_fornecedor=".$rsParticipantes->getCampo('inCgmParticipante')." \n";
          $obLicParCert->recuperaRelacionamento ( $rsPar , $stFiltro , '' );
          // valor
          $rsParConvenio = new RecordSet();
          $obParConvenio->setDado('cgm_fornecedor'         ,$rsPar->getCampo( 'cgm_fornecedor' )                       );
          $obParConvenio->setDado('num_convenio'           , $_REQUEST['inNumConvenio']                                );
          $obParConvenio->setDado('num_certificacao'       , $rsPar->getCampo( 'num_certificacao' )                    );
          $obParConvenio->setDado('exercicio'              , $_REQUEST['inExercicio']                                  );
          $obParConvenio->setDado('cgm_fornecedor'         , $rsPar->getCampo( 'cgm_fornecedor' )                      );
          $obParConvenio->setDado('exercicio_certificacao' , $rsPar->getCampo( 'exercicio' )                           );
          $obParConvenio->setDado('cgm_fornecedor'         , $rsPar->getCampo( 'cgm_fornecedor' )                      );
          $obParConvenio->setDado('cod_tipo_participante'  , $rsParticipantes->getCampo ( 'inCodTipoParticipante' )    );
          $obParConvenio->setDado('valor_participacao'     , $rsParticipantes->getCampo ( 'nuValorParticipacao' )      );
          $obParConvenio->setDado('percentual_participacao', $rsParticipantes->getCampo ( 'hdnPercentualParticipacao' ));
          $obParConvenio->setDado('funcao'                 , $rsParticipantes->getCampo ( 'stFuncaoParticipante'      ));

         $obParConvenio->inclusao();
         $rsParticipantes->proximo();
        }
        Sessao::write('arRequest',$_REQUEST);

        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Convênio: ".$_REQUEST['inNumConvenio'],"incluir","aviso", Sessao::getId(), "../");

        SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());

        Sessao::encerraExcecao();

    break;

    case 'anular':
        if ($_REQUEST[ 'inNumConvenio']) {
            require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoConvenioAnulado.class.php' );
            $obConvenioAnulado = new TLicitacaoConvenioAnulado;

            Sessao::setTrataExcecao( true );
            Sessao::getTransacao()->setMapeamento( $obConvenioAnulado );

            $obConvenioAnulado = new TLicitacaoConvenioAnulado;
            $obConvenioAnulado->setDado ( 'num_convenio' , $_REQUEST[ 'inNumConvenio'] );
            $obConvenioAnulado->setDado ( 'exercicio' , Sessao::getExercicio() );
            $obConvenioAnulado->setDado ( 'justificativa' , $_REQUEST[ 'stJustificativa' ] );
            $obConvenioAnulado->setDado ( 'dt_anulacao' , $_REQUEST[ 'dtAnulacao' ] );
            $obConvenioAnulado->inclusao();

            sistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Convênio: ".$_REQUEST['inNumConvenio'],"incluir","aviso", Sessao::getId(), "../");

            Sessao::encerraExcecao();
        }
    break;

    case "rescindir":
        require_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoRescisaoConvenio.class.php";
        require_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoPublicacaoRescisaoConvenio.class.php";

        $arValores = Sessao::read('arValores');
        if ( ( $arValores == null ) || (count($arValores) < 1) ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Veículo de Publicidade.", "n_incluir", "erro" );
            exit;
        }

        // verifica se está setado no REQUESTS inNumRescisao.
        // Isso identifica se existe registro ou não já na tabela para
        // esse convenio e exercicio
        if (!isset($_REQUEST["inNumRescisao"])) {

            // Se entrou quer dizer que não está setado o campo, com isso deve incluir na tabela

            $obRescisaoConvenio = new TLicitacaoRescisaoConvenio;
            $obRescisaoConvenio->setDado( "num_convenio", $_REQUEST["inNumConvenio"]);
            $obRescisaoConvenio->setDado( "exercicio_convenio", $_REQUEST["inExercicio"]);
            $obRescisaoConvenio->recuperaProximoNumConvenio($rsProxNumConvenio);
            $obRescisaoConvenio->setDado( "num_rescisao", $rsProxNumConvenio->getCampo("prox_num_rescisao"));
            $obRescisaoConvenio->setDado( "exercicio", Sessao::getExercicio() );
            $obRescisaoConvenio->setDado( "responsavel_juridico", $_REQUEST["inCgmResponsavelJuridico"]);
            $obRescisaoConvenio->setDado( "dt_rescisao", $_REQUEST["dtRescisao"]);
            $obRescisaoConvenio->setDado( "vlr_multa", $_REQUEST["vlMulta"]);
            $obRescisaoConvenio->setDado( "vlr_indenizacao", $_REQUEST["vlIndenizacao"]);
            $obRescisaoConvenio->setDado( "motivo", $_REQUEST["stMotivo"]);
            $obRescisaoConvenio->inclusao();

            $obTLicitacaoPublicacaoRescisaoConvenio = new TLicitacaoPublicacaoRescisaoConvenio();
            //inclui os dados da publicacao da rescisão
            foreach ($arValores as $arTemp) {
                $obTLicitacaoPublicacaoRescisaoConvenio->setDado( 'num_convenio'       , $obRescisaoConvenio->getDado('num_convenio') );
                $obTLicitacaoPublicacaoRescisaoConvenio->setDado( 'cgm_imprensa'       , $arTemp['inVeiculo'] );
                $obTLicitacaoPublicacaoRescisaoConvenio->setDado( 'dt_publicacao'      , $arTemp['dtDataPublicacao'] );
                $obTLicitacaoPublicacaoRescisaoConvenio->setDado( 'num_publicacao'     , $arTemp['inNumPublicacao'] );
                $obTLicitacaoPublicacaoRescisaoConvenio->setDado( 'exercicio_convenio' , $obRescisaoConvenio->getDado("exercicio_convenio"));
                $obTLicitacaoPublicacaoRescisaoConvenio->setDado( 'observacao'         , $arTemp['_stObservacao'] );
                $obTLicitacaoPublicacaoRescisaoConvenio->inclusao();
            }

            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=rescindir","Convênio: ".$_REQUEST['inNumConvenio']."/".$_REQUEST['inExercicio'],"incluir","aviso", Sessao::getId(), "../");

        } else {
            /*
                Se entrou aqui é porque já existe registro para aquele exercicio_convenio
                e num_convenio, então atualiza os dados.
            */

            $obRescisaoConvenio = new TLicitacaoRescisaoConvenio;
            $obRescisaoConvenio->setDado( "exercicio", $_REQUEST["inExercicioRescisao"] );
            $obRescisaoConvenio->setDado( "num_rescisao", $_REQUEST["inNumRescisao"]);
            $obRescisaoConvenio->setDado( "exercicio_convenio", $_REQUEST["inExercicio"]);
            $obRescisaoConvenio->setDado( "num_convenio", $_REQUEST["inNumConvenio"]);
            $obRescisaoConvenio->setDado( "responsavel_juridico", $_REQUEST["inCgmResponsavelJuridico"]);
            $obRescisaoConvenio->setDado( "dt_rescisao", $_REQUEST["dtRescisao"]);
            $obRescisaoConvenio->setDado( "vlr_multa", $_REQUEST["vlMulta"]);
            $obRescisaoConvenio->setDado( "vlr_indenizacao", $_REQUEST["vlIndenizacao"]);
            $obRescisaoConvenio->setDado( "motivo", $_REQUEST["stMotivo"]);
            $obRescisaoConvenio->alteracao();

            /* Exclui os dados da tabela de acordo com o
                exercicio_convenio e num_convenio
            */
            $obPublicacaoRescisao = new TLicitacaoPublicacaoRescisaoConvenio;
            $obPublicacaoRescisao->setDado("num_convenio", $_REQUEST["inNumConvenio"]);
            $obPublicacaoRescisao->setDado("exercicio_convenio", $_REQUEST["inExercicio"]);
            $obPublicacaoRescisao->exclusao();

            /*
                Inclui os dados da listagem nova
            */
            $rsVeiculos->setPrimeiroElemento();
            while ( !$rsVeiculos->eof() ) {
                $obPublicacaoRescisao->setDado("cgm_imprensa", $rsVeiculos->getCampo("inCgmVeiculoPublicidade"));
                $obPublicacaoRescisao->setDado("dt_publicacao", $rsVeiculos->getCampo("dtPublicacaoRescisao"));
                $obPublicacaoRescisao->setDado("observacao", $rsVeiculos->getCampo("stObsPublicacao"));
                $obPublicacaoRescisao->inclusao();
                $rsVeiculos->proximo();
            }
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=rescindir","Convênio: ".$_REQUEST['inNumConvenio']."/".$_REQUEST['inExercicio'],"alterar","aviso", Sessao::getId(), "../");
        }
    break;
}
