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
	* Processar do Formulario de Convenio TCEMG
	* Data de Criação   : 10/03/2014

	* @author Analista: Sergio Luiz dos Santos
	* @author Desenvolvedor: Michel Teixeira
	* @ignore

	$Id: PRManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

	*Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoParticipanteConvenio.class.php' );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenio.class.php'              );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioEmpenho.class.php'       );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioParticipante.class.php'  );
include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoPublicacaoConvenio.class.php'   );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioAditivo.class.php'       );

$stAcao = $request->get("stAcao");

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case 'incluir' :
        // validar participantes e empenhos
        $arEmpenhos		= Sessao::read( 'arEmpenhos'	);
        $rsParticipantes= Sessao::read( 'participantes'	);

        if ( count($rsParticipantes) < 1 ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Participante.", "n_incluir", "erro" );
            exit;
        }

        if ( count($arEmpenhos) < 1 ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Empenho.", "n_incluir", "erro" );
            exit;
        }

        // preparar datas
        $dtAssinatura		= $_REQUEST[ 'dtAssinatura'		];
        $dtFinalVigencia	= $_REQUEST[ 'dtFinalVigencia'	];
        $dtInicioExecucao	= $_REQUEST[ 'dtInicioExecucao'	];

        // valor
        $nuValorConvenio = str_replace ( ',' , '.' , str_replace ( '.' , '' , $_REQUEST[ 'nuValorConvenio'	] ) );
        $nuValorContra   = str_replace ( ',' , '.' , str_replace ( '.' , '' , $_REQUEST[ 'nuValorContra' 	] ) );

        $somaValorParticipantes = 0;
        foreach ($rsParticipantes->arElementos as $arParticipante) {
            $somaValorParticipantes = $somaValorParticipantes + $arParticipante['nuValorParticipacao'];
        }

        if ($somaValorParticipantes != $nuValorConvenio){
            SistemaLegado::exibeAviso("A soma do Valor de Participações deve ser igual a 100% do Valor do Convênio.", "n_incluir", "erro" );
            exit;
        }

        $obTTCEMGConvenio = new TTCEMGConvenio();

        // verificando se ja existe o número do convenio
        $stFiltro  = " WHERE nro_convenio	= ".$_REQUEST['inNumConvenio'];
        $stFiltro .= " AND exercicio		= '".$_REQUEST['stExercicio']."'";
        $stFiltro .= " AND cod_entidade		= ".$_REQUEST['cod_entidade'];
        $obTTCEMGConvenio->recuperaTodos ( $rsConvenio, $stFiltro );

        if ( $rsConvenio->getNumLinhas() > 0 ) {
            SistemaLegado::exibeAviso( "Já existe um convênio cadastrado com este número.", "n_incluir", "erro" );
            exit;
        }

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTTCEMGConvenio );

        // convenio
        $obTTCEMGConvenio->recuperaProximoConvenio($rsRecordSet);
        $inCodConvenio = $rsRecordSet->getCampo('cod_convenio');
        if(!$inCodConvenio)
            $inCodConvenio = 1;            
        
        $obTTCEMGConvenio->setDado( 'cod_convenio'        , $inCodConvenio                  );
        $obTTCEMGConvenio->setDado( "cod_entidade"        , $_REQUEST['cod_entidade']       );
        $obTTCEMGConvenio->setDado( "nro_convenio"        , $_REQUEST['inNumConvenio']      );
        $obTTCEMGConvenio->setDado( "exercicio"           , $_REQUEST['stExercicio']        );
        $obTTCEMGConvenio->setDado( "data_assinatura"     , $dtAssinatura                   );
        $obTTCEMGConvenio->setDado( "data_inicio"         , $_REQUEST['dtInicioExecucao']   );
        $obTTCEMGConvenio->setDado( "data_final"          , $_REQUEST['dtFinalVigencia']    );
        $obTTCEMGConvenio->setDado( "vl_convenio"         , $nuValorConvenio                );
        $obTTCEMGConvenio->setDado( "vl_contra_partida"   , $nuValorContra                  );
        $obTTCEMGConvenio->setDado( "cod_objeto"          , $_REQUEST['stObjeto']           );

        $obTTCEMGConvenio->inclusao();

        //inclui os dados da publicacao do convênio
        $obTTTCEMGConvenioEmpenho = new TTCEMGConvenioEmpenho();
        
        $obTTTCEMGConvenioEmpenho->setDado( 'cod_convenio'	, $inCodConvenio            );
        $obTTTCEMGConvenioEmpenho->setDado( 'cod_entidade'	, $_REQUEST['cod_entidade'] );
        $obTTTCEMGConvenioEmpenho->setDado( 'exercicio'		, $_REQUEST['stExercicio']  );
        
        foreach ($arEmpenhos as $arTemp) {
            $obTTTCEMGConvenioEmpenho->setDado( 'cod_empenho'       , $arTemp['cod_empenho']	);
            $obTTTCEMGConvenioEmpenho->setDado( 'exercicio_empenho' , $arTemp['exercicio']		);
            
            $obTTTCEMGConvenioEmpenho->inclusao();
        }

        $obTTCEMGConvenioParticipante = new TTCEMGConvenioParticipante();
        $rsParticipantes->setPrimeiroElemento();
        
        $obTTCEMGConvenioParticipante->setDado ( 'exercicio'     , $_REQUEST['stExercicio']  );
        $obTTCEMGConvenioParticipante->setDado ( 'cod_convenio'  , $inCodConvenio            );
        $obTTCEMGConvenioParticipante->setDado ( 'cod_entidade'  , $_REQUEST['cod_entidade'] );
        while ( !$rsParticipantes->eof() ) {
            $obTTCEMGConvenioParticipante->setDado ( 'cgm_participante'             , $rsParticipantes->getCampo('inCgmParticipante')           );
            $obTTCEMGConvenioParticipante->setDado ( 'cod_tipo_participante'        , $rsParticipantes->getCampo( 'inCodTipoParticipante' )     );
            $obTTCEMGConvenioParticipante->setDado ( 'vl_concedido'                 , $rsParticipantes->getCampo( 'nuValorParticipacao' )       );
            $obTTCEMGConvenioParticipante->setDado ( 'percentual'                   , $rsParticipantes->getCampo( 'nuPercentualParticipacao' )  );
            $obTTCEMGConvenioParticipante->setDado ( 'esfera'                       , $rsParticipantes->getCampo( 'stEsfera' )                  );

            $obTTCEMGConvenioParticipante->inclusao();

            $rsParticipantes->proximo();
        }
        
        $obTTCEMGConvenioAditivo = new TTCEMGConvenioAditivo ;
        $obTTCEMGConvenioAditivo->setDado( 'cod_convenio'   , $inCodConvenio            );
        $obTTCEMGConvenioAditivo->setDado( 'exercicio'      , $_REQUEST['stExercicio']  );
        $obTTCEMGConvenioAditivo->setDado( 'cod_entidade'   , $_REQUEST['cod_entidade'] );
        
        $arAditivo = Sessao::read('arAditivo');
        
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($arAditivo);
        
        while( !$rsRecordSet->eof() ){
            $obTTCEMGConvenioAditivo->setDado( 'cod_aditivo'    , $rsRecordSet->getCampo('inCodAditivo')        );
            $obTTCEMGConvenioAditivo->setDado( 'descricao'      , $rsRecordSet->getCampo('stDescAditivo')       );
            $obTTCEMGConvenioAditivo->setDado( 'data_assinatura', $rsRecordSet->getCampo('dtAssinaturaAditivo') );
            $obTTCEMGConvenioAditivo->setDado( 'data_final'     , $rsRecordSet->getCampo('dtFinalAditivo')      );
            $obTTCEMGConvenioAditivo->setDado( 'vl_convenio'    , $rsRecordSet->getCampo('nuValorAditivo')      );
            $obTTCEMGConvenioAditivo->setDado( 'vl_contra'      , $rsRecordSet->getCampo('nuValorContraAditivo'));

            $obTTCEMGConvenioAditivo->inclusao();
            
            $rsRecordSet->proximo();
        }

        Sessao::write('arRequest',$_REQUEST);

        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Convênio: ".$_REQUEST['inNumConvenio'],"incluir","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
    break;

    case 'excluirConvenio':
        $inNumConvenio  = (isset($_REQUEST[ 'inNumConvenio' ])) ? $_REQUEST[ 'inNumConvenio'  ] : '';
        $inCodConvenio  = (isset($_REQUEST[ 'inCodConvenio' ])) ? $_REQUEST[ 'inCodConvenio'  ] : '';
        $stExercicio    = (isset($_REQUEST[ 'inExercicio'   ])) ? $_REQUEST[ 'inExercicio'    ] : '';
        $inCodEntidade  = (isset($_REQUEST[ 'inCodEntidade' ])) ? $_REQUEST[ 'inCodEntidade'  ] : '';
        if($inNumConvenio!=''&&$inCodConvenio!=''&&$stExercicio!=''&&$inCodEntidade!=''){
            Sessao::setTrataExcecao( true );
            Sessao::getTransacao()->setMapeamento( $obConvenio );
    
            $obTTCEMGConvenioParticipante = new TTCEMGConvenioParticipante();
            
            $obTTCEMGConvenioParticipante->setDado ( 'exercicio'     , $stExercicio     );
            $obTTCEMGConvenioParticipante->setDado ( 'cod_convenio'  , $inCodConvenio   );
            $obTTCEMGConvenioParticipante->setDado ( 'cod_entidade'  , $inCodEntidade   );
    
            $obErro = $obTTCEMGConvenioParticipante->exclusao();
                    
            if( !$obErro->ocorreu() ){
                $obTTTCEMGConvenioEmpenho = new TTCEMGConvenioEmpenho();
                
                $obTTTCEMGConvenioEmpenho->setDado( 'cod_convenio'	, $inCodConvenio    );
                $obTTTCEMGConvenioEmpenho->setDado( 'cod_entidade'	, $inCodEntidade    );
                $obTTTCEMGConvenioEmpenho->setDado( 'exercicio'     , $stExercicio      );
                
                $obErro = $obTTTCEMGConvenioEmpenho->exclusao();
                
                if( !$obErro->ocorreu() ){
                    $obTTCEMGConvenioAditivo = new TTCEMGConvenioAditivo ;
                    $obTTCEMGConvenioAditivo->setDado( 'cod_convenio'   , $inCodConvenio    );
                    $obTTCEMGConvenioAditivo->setDado( 'exercicio'      , $stExercicio      );
                    $obTTCEMGConvenioAditivo->setDado( 'cod_entidade'   , $inCodEntidade    );
                    
                    $obErro = $obTTCEMGConvenioAditivo->exclusao();
                }
                
                if( !$obErro->ocorreu() ){
                    $obTTCEMGConvenio = new TTCEMGConvenio();
                    $obTTCEMGConvenio->setDado( 'cod_convenio'        , $inCodConvenio  );
                    $obTTCEMGConvenio->setDado( "cod_entidade"        , $inCodEntidade  );
                    $obTTCEMGConvenio->setDado( "exercicio"           , $stExercicio    );
                    
                    $obErro = $obTTCEMGConvenio->exclusao();
                }
            }
            
            if( $obErro->ocorreu() ){
            	sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }else{
                sistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=excluir","Convênio: ".$inNumConvenio,"incluir","aviso", Sessao::getId(), "../");
            }
            Sessao::encerraExcecao();
        }
    break;

    case 'alterar':
        
        // validar participantes e empenhos
        $arEmpenhos		= Sessao::read( 'arEmpenhos'	);
        $rsParticipantes= Sessao::read( 'participantes'	);
        
        $obErro = new Erro();

        if ( count($rsParticipantes) < 1 ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Participante.", "n_incluir", "erro" );
            exit;
        }

        if ( count($arEmpenhos) < 1 ) {
            SistemaLegado::exibeAviso( "Deve haver ao menos 1 Empenho.", "n_incluir", "erro" );
            exit;
        }

        // preparar datas
        $dtAssinatura		= $_REQUEST[ 'dtAssinatura'		];
        $dtFinalVigencia	= $_REQUEST[ 'dtFinalVigencia'	];
        $dtInicioExecucao	= $_REQUEST[ 'dtInicioExecucao'	];

        // valor
        $nuValorConvenio = str_replace ( ',' , '.' , str_replace ( '.' , '' , $_REQUEST[ 'nuValorConvenio'	] ) );
        $nuValorContra   = str_replace ( ',' , '.' , str_replace ( '.' , '' , $_REQUEST[ 'nuValorContra' 	] ) );

        $somaValorParticipantes = 0;
        foreach ($rsParticipantes->arElementos as $arParticipante) {
            $somaValorParticipantes = $somaValorParticipantes + $arParticipante['nuValorParticipacao'];
        }

        if ($somaValorParticipantes != $nuValorConvenio){
            SistemaLegado::exibeAviso("A soma do Valor de Participações deve ser igual a 100% do Valor do Convênio.", "n_incluir", "erro" );
            exit;
        }

        $obTTCEMGConvenio = new TTCEMGConvenio();

        // verificando se ja existe o número do convenio
		$stFiltro  = " WHERE cod_convenio	= ".$_REQUEST['cod_convenio'];
		$stFiltro .= " AND exercicio		= '".$_REQUEST['stExercicio']."'";
		$stFiltro .= " AND cod_entidade		= ".$_REQUEST['cod_entidade'];
        $obTTCEMGConvenio->recuperaTodos ( $rsConvenio, $stFiltro );

        if ( $rsConvenio->getNumLinhas() < 1 ) {
            SistemaLegado::exibeAviso( "Convênio não localizado.", "n_incluir", "erro" );
            exit;
        }else{
            if($rsConvenio->getCampo('nro_convenio')!=$_REQUEST['hdnNumConvenio']){
                $stFiltro  = " WHERE nro_convenio	= ".$_REQUEST['hdnNumConvenio'];
                $stFiltro .= " AND exercicio		= '".$_REQUEST['stExercicio']."'";
                $stFiltro .= " AND cod_entidade		= ".$_REQUEST['cod_entidade'];
                $obTTCEMGConvenio->recuperaTodos ( $rsConvenio, $stFiltro );
                
                if ( $rsConvenio->getNumLinhas() > 0 ) {
                    SistemaLegado::exibeAviso( "Já existe um convênio cadastrado com este número.", "n_incluir", "erro" );
                    exit;
                }
            }
        }

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTTCEMGConvenio );

        // convenio
        $obTTCEMGConvenio->setDado( 'cod_convenio'        , $_REQUEST['cod_convenio']       );
        $obTTCEMGConvenio->setDado( "cod_entidade"        , $_REQUEST['cod_entidade']       );
        $obTTCEMGConvenio->setDado( "nro_convenio"        , $_REQUEST['hdnNumConvenio']     );
        $obTTCEMGConvenio->setDado( "exercicio"           , $_REQUEST['stExercicio']        );
        $obTTCEMGConvenio->setDado( "data_assinatura"     , $dtAssinatura                   );
        $obTTCEMGConvenio->setDado( "data_inicio"         , $_REQUEST['dtInicioExecucao']   );
        $obTTCEMGConvenio->setDado( "data_final"          , $_REQUEST['dtFinalVigencia']    );
        $obTTCEMGConvenio->setDado( "vl_convenio"         , $nuValorConvenio                );
        $obTTCEMGConvenio->setDado( "vl_contra_partida"   , $nuValorContra                  );
        $obTTCEMGConvenio->setDado( "cod_objeto"          , $_REQUEST['stObjeto']           );
        
        $obErro = $obTTCEMGConvenio->alteracao();

        if( !$obErro->ocorreu() ){
            //inclui os dados da publicacao do convênio
            $obTTTCEMGConvenioEmpenho = new TTCEMGConvenioEmpenho();
            
            $obTTTCEMGConvenioEmpenho->setDado( 'cod_convenio'	, $_REQUEST['cod_convenio'] );
            $obTTTCEMGConvenioEmpenho->setDado( 'cod_entidade'	, $_REQUEST['cod_entidade'] );
            $obTTTCEMGConvenioEmpenho->setDado( 'exercicio'		, $_REQUEST['stExercicio']  );
            
            $obErro = $obTTTCEMGConvenioEmpenho->exclusao();
            
            if( !$obErro->ocorreu() ){
                $obTTTCEMGConvenioEmpenho->setDado( 'cod_convenio'	, $_REQUEST['cod_convenio'] );
                $obTTTCEMGConvenioEmpenho->setDado( 'cod_entidade'	, $_REQUEST['cod_entidade'] );
                $obTTTCEMGConvenioEmpenho->setDado( 'exercicio'		, $_REQUEST['stExercicio']  );
                
                foreach ($arEmpenhos as $arTemp) {
                    $obTTTCEMGConvenioEmpenho->setDado( 'cod_empenho'       , $arTemp['cod_empenho']    );
                    $obTTTCEMGConvenioEmpenho->setDado( 'exercicio_empenho' , $arTemp['exercicio']      );
                    
                    $obErro = $obTTTCEMGConvenioEmpenho->inclusao();
                }
            }

            if( !$obErro->ocorreu() ){
                $obTTCEMGConvenioParticipante = new TTCEMGConvenioParticipante();
                $rsParticipantes->setPrimeiroElemento();
                
                $obTTCEMGConvenioParticipante->setDado ( 'exercicio'     , $_REQUEST['stExercicio']  );
                $obTTCEMGConvenioParticipante->setDado ( 'cod_convenio'  , $_REQUEST['cod_convenio'] );
                $obTTCEMGConvenioParticipante->setDado ( 'cod_entidade'  , $_REQUEST['cod_entidade'] );
                
                $obErro = $obTTCEMGConvenioParticipante->exclusao();
                
                if( !$obErro->ocorreu() ){
                    $obTTCEMGConvenioParticipante->setDado ( 'exercicio'     , $_REQUEST['stExercicio']  );
                    $obTTCEMGConvenioParticipante->setDado ( 'cod_convenio'  , $_REQUEST['cod_convenio'] );
                    $obTTCEMGConvenioParticipante->setDado ( 'cod_entidade'  , $_REQUEST['cod_entidade'] );
                
                    while ( !$rsParticipantes->eof() ) {
                        $obTTCEMGConvenioParticipante->setDado ( 'cgm_participante'             , $rsParticipantes->getCampo('inCgmParticipante')           );
                        $obTTCEMGConvenioParticipante->setDado ( 'cod_tipo_participante'	, $rsParticipantes->getCampo( 'inCodTipoParticipante' )     );
                        $obTTCEMGConvenioParticipante->setDado ( 'vl_concedido'                 , $rsParticipantes->getCampo( 'nuValorParticipacao' )       );
                        $obTTCEMGConvenioParticipante->setDado ( 'percentual'                   , $rsParticipantes->getCampo( 'nuPercentualParticipacao' )  );
                        $obTTCEMGConvenioParticipante->setDado ( 'esfera'                       , $rsParticipantes->getCampo( 'stEsfera' )                  );
            
                        $obErro = $obTTCEMGConvenioParticipante->inclusao();
            
                        $rsParticipantes->proximo();
                    }
                }
            }
            
            if( !$obErro->ocorreu() ){
                $obTTCEMGConvenioAditivo = new TTCEMGConvenioAditivo ;
                $obTTCEMGConvenioAditivo->setDado( 'cod_convenio'   , $_REQUEST['cod_convenio'] );
                $obTTCEMGConvenioAditivo->setDado( 'exercicio'      , $_REQUEST['stExercicio']  );
                $obTTCEMGConvenioAditivo->setDado( 'cod_entidade'   , $_REQUEST['cod_entidade'] );
                
                $obErro = $obTTCEMGConvenioAditivo->exclusao();
                
                if( !$obErro->ocorreu() ){
                    $arAditivo = Sessao::read('arAditivo');
                    
                    $rsRecordSet = new RecordSet;
                    $rsRecordSet->preenche($arAditivo);
                    
                    while( !$rsRecordSet->eof() ){
                        $obTTCEMGConvenioAditivo->setDado( 'cod_aditivo'    , $rsRecordSet->getCampo('inCodAditivo')        );
                        $obTTCEMGConvenioAditivo->setDado( 'descricao'      , $rsRecordSet->getCampo('stDescAditivo')       );
                        $obTTCEMGConvenioAditivo->setDado( 'data_assinatura', $rsRecordSet->getCampo('dtAssinaturaAditivo') );
                        $obTTCEMGConvenioAditivo->setDado( 'data_final'     , $rsRecordSet->getCampo('dtFinalAditivo')      );
                        $obTTCEMGConvenioAditivo->setDado( 'vl_convenio'    , $rsRecordSet->getCampo('nuValorAditivo')      );
                        $obTTCEMGConvenioAditivo->setDado( 'vl_contra'      , $rsRecordSet->getCampo('nuValorContraAditivo'));
            
                        $obErro = $obTTCEMGConvenioAditivo->inclusao();
                        
                        $rsRecordSet->proximo();
                    }
                }
            }
        }
        
        if( $obErro->ocorreu() ){
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }else{
            Sessao::write('arRequest',$_REQUEST);
    
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Convênio: ".$_REQUEST['hdnNumConvenio'],"incluir","aviso", Sessao::getId(), "../");
        }
        Sessao::encerraExcecao();
    break;
}
?>