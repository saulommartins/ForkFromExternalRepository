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
/*
    * Formulário de configuração de Obras e Serviços de Engenharia
    * Data de Criação   : 11/09/2015
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: FMManterConfiguracaoObrasServicos.php 63809 2015-10-19 16:52:56Z lisiane $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObra.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoObra.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoFuncaoObra.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasModalidade.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASituacaoObra.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAMedidasObra.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoContratacaoObra.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoCEP.class.php';
include_once TLIC.'TLicitacaoLicitacao.class.php';
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObrasServicos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

Sessao::write('arAndamento' , array());
Sessao::write('arFiscal'    , array());
Sessao::write('arMedicao'   , array());
Sessao::write('arContrato'  , array());

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$arLink = Sessao::read('arLink');

if ( count($arLink) > 0 ) {
        $stLink = '';
        foreach ($arLink as $stCampo => $stValor) {
            if (is_array($stValor)) {
                foreach ($stValor as $stCampo2 => $stValor2) {
                    $stLink .= "&".$stCampo2."=".@urlencode( $stValor2 );
                }
            } else {
                $stLink .= "&".$stCampo."=".urlencode( $stValor );
            }
        }
    }

$rsCEP = new RecordSet();
$rsLicitacao = new RecordSet();

if ($stAcao == 'alterar') {
    $stFiltro  = " WHERE obra.cod_entidade = ".$request->get('inCodEntidade');
    $stFiltro .= "   AND obra.exercicio = '".$request->get('stExercicio')."'";
    $stFiltro .= "   AND obra.cod_tipo = ".$request->get('inCodTipo');
    $stFiltro .= "   AND obra.cod_obra = ".$request->get('inCodObra');

    $obTTCMBAObra = new TTCMBAObra;
    $obTTCMBAObra->recuperaObra($rsObra, $stFiltro);

    if ( $rsObra->getNumLinhas() == 1) {
        $obTCEP = new TCEP();
        $obTCEP->setDado('cep', $rsObra->getCampo('cep'));
        $obTCEP->recuperaCepBairro($rsCEP);

        $inCep = $rsObra->getCampo('cep');
        for($i = 0; $i<=strlen($inCep)-1; $i++){
            if($i==5)
                $stCep .= '-'.$inCep[$i];
            else
                $stCep .= $inCep[$i];
        }

        $request->set('stLocal'             , $rsObra->getCampo('local'));
        $request->set('inCEP'               , $inCep);
        $request->set('stCEP'               , $stCep);
        $request->set('inCodBairro'         , $rsObra->getCampo('cod_uf').'_'.$rsObra->getCampo('cod_municipio').'_'.$rsObra->getCampo('cod_bairro'));
        $request->set('inCodTipoFuncaoObra' , $rsObra->getCampo('cod_funcao'));
        $request->set('stNroObra'           , $rsObra->getCampo('nro_obra'));
        $request->set('stDescricao'         , $rsObra->getCampo('descricao'));
        $request->set('nuVlObra'            , number_format($rsObra->getCampo('vl_obra'), 2, ",", "."));
        $request->set('dtCadastro'          , $rsObra->getCampo('data_cadastro'));
        $request->set('dtInicio'            , $rsObra->getCampo('data_inicio'));
        $request->set('dtAceite'            , $rsObra->getCampo('data_aceite'));
        $request->set('dtPrazo'             , $rsObra->getCampo('data_prazo'));
        $request->set('dtRecebimento'       , $rsObra->getCampo('data_recebimento'));
        $request->set('stExercicioLicitacao', (is_numeric($rsObra->getCampo('exercicio_licitacao'))) ? $rsObra->getCampo('exercicio_licitacao') : Sessao::getExercicio());
        $request->set('inCodModalidade'     , $rsObra->getCampo('cod_modalidade'));
        $request->set('inCodLicitacao'      , $rsObra->getCampo('cod_licitacao'));

        if($rsObra->getCampo('st_licitacao')!=''){
            $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
            $obTLicitacaoLicitacao->setDado( 'exercicio'        , $rsObra->getCampo('exercicio_licitacao'));
            $obTLicitacaoLicitacao->setDado( 'cod_entidade'     , $request->get('inCodEntidade'));
            $obTLicitacaoLicitacao->setDado( 'cod_modalidade'   , $rsObra->getCampo('cod_modalidade'));
            $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );
        }

        $jsOnLoad .= "montaParametrosGET('montaObra'); \n";
    }
}

$stFiltro = "";
$stOrder = " ORDER BY descricao ";
$obTTCMBATipoObra = new TTCMBATipoObra;
$obTTCMBATipoObra->recuperaTodos($rsTipoObra, $stFiltro, $stOrder);

$obTTCMBATipoFuncaoObra = new TTCMBATipoFuncaoObra;
$obTTCMBATipoFuncaoObra->recuperaTodos($rsTipoFuncaoObra, $stFiltro, $stOrder);

$obTTCMBASituacaoObra = new TTCMBASituacaoObra;
$obTTCMBASituacaoObra->recuperaTodos($rsSituacaoObra, $stFiltro, $stOrder);

$obTTCMBAMedidasObra = new TTCMBAMedidasObra;
$obTTCMBAMedidasObra->recuperaTodos($rsMedidasObra, $stFiltro, $stOrder);

$obTTCMBATipoContratacaoObra = new TTCMBATipoContratacaoObra;
$obTTCMBATipoContratacaoObra->recuperaTodos($rsTipoContratacao, $stFiltro, $stOrder);

//Consulta para Buscar Modalidades Licitação
$stOrdem  = " ORDER BY cod_modalidade, descricao ";
$obComprasModalidade = new TComprasModalidade();
$obComprasModalidade->recuperaTodos($rsModalidade, $stFiltro, $stOrdem);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnCodObra = new Hidden;
$obHdnCodObra->setName ( "inCodObra" );
$obHdnCodObra->setId   ( "inCodObra" );
$obHdnCodObra->setValue( $request->get('inCodObra') );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setId   ( "stExercicio" );
$obHdnExercicio->setValue( $request->get('stExercicio') );

$obHdnIdAndamento = new Hidden;
$obHdnIdAndamento->setName ( "inIdAndamento" );
$obHdnIdAndamento->setId   ( "inIdAndamento" );

$obHdnIdFiscal = new Hidden;
$obHdnIdFiscal->setName ( "inIdFiscal" );
$obHdnIdFiscal->setId   ( "inIdFiscal" );

$obHdnIdMedicao = new Hidden;
$obHdnIdMedicao->setName ( "inIdMedicao" );
$obHdnIdMedicao->setId   ( "inIdMedicao" );

$obHdnIdContrato = new Hidden;
$obHdnIdContrato->setName ( "inIdContrato" );
$obHdnIdContrato->setId   ( "inIdContrato" );

#Dados de Obras

$obEntidade = new ITextBoxSelectEntidadeUsuario;
$obEntidade->setCodEntidade($request->get('inCodEntidade'));
$obEntidade->setNull( false );
$obEntidade->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");
$obEntidade->obSelect->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");
if ($stAcao == 'alterar')
    $obEntidade->setLabel(true);

$obCmbTipoObra = new Select();
$obCmbTipoObra->setName         ( "inCodTipoObra"                   );
$obCmbTipoObra->setRotulo       ( "Tipo Obra"                       );
$obCmbTipoObra->setId           ( "stTipoObra"                      );
$obCmbTipoObra->setCampoId      ( "cod_tipo"                        );
$obCmbTipoObra->setCampoDesc    ( "descricao"                       );
$obCmbTipoObra->addOption       ( '','Selecione'                    );
$obCmbTipoObra->preencheCombo   ( $rsTipoObra                       );
$obCmbTipoObra->setNull         ( false                             );
$obCmbTipoObra->setValue        ( $request->get('inCodTipo')        );
if ($stAcao == 'alterar')
    $obCmbTipoObra->setLabel(true);

$obTxtLocal = new TextBox;
$obTxtLocal->setName     ( "stLocal"                );
$obTxtLocal->setId       ( "stLocal"                );
$obTxtLocal->setValue    ( $request->get('stLocal') );
$obTxtLocal->setRotulo   ( "Local"                  );
$obTxtLocal->setTitle    ( "Informe o Local."       );
$obTxtLocal->setNull     ( false                    );
$obTxtLocal->setSize     ( 40                       );
$obTxtLocal->setMaxLength( 50                       );

$obTxtCEP = new BuscaInner;
$obTxtCEP->setRotulo                ( "CEP"                     );
$obTxtCEP->setTitle                 ( "Informe o CEP."          );
$obTxtCEP->setNull                  ( false                     );
$obTxtCEP->setId                    ( "stCEP"                   );
$obTxtCEP->setValue                 ( $request->get("stCEP")    );
$obTxtCEP->obCampoCod->setName      ( "inCEP"                   );
$obTxtCEP->obCampoCod->setId        ( "inCEP"                   );
$obTxtCEP->obCampoCod->setSize      ( 10                        );
$obTxtCEP->obCampoCod->setMaxLength ( 8                         );
$obTxtCEP->obCampoCod->setValue     ( $request->get("inCEP")    );
$obTxtCEP->obCampoCod->setAlign     ( "left"                    );
$obTxtCEP->setFuncaoBusca("abrePopUp('".CAM_GPC_TCMBA_POPUPS."configuracao/FLCep.php','frm','inCEP','stCEP','','".Sessao::getId()."','800','550');");
$obTxtCEP->obCampoCod->obEvento->setOnBlur("if(this.value != jQuery('input[name=HdninCEP]').val()) { montaParametrosGET('montaBairro');}");

$obCmbBairro = new Select();
$obCmbBairro->setName   ( "inCodBairro"                                     );
$obCmbBairro->setRotulo ( "Bairro"                                          );
$obCmbBairro->setId     ( "inCodBairro"                                     );
$obCmbBairro->addOption ( '','Selecione'                                    );
$obCmbBairro->setNull   ( false                                             );
$obCmbBairro->setValue  ( $request->get('inCodBairro')                      );
$obCmbBairro->setCampoId    ( "[cod_uf]_[cod_municipio]_[cod_bairro]"       );
$obCmbBairro->setCampoDesc  ( "[nom_uf] / [nom_municipio] / [nom_bairro]"   );
$obCmbBairro->preencheCombo ( $rsCEP                                        );

$obCmbTipoFuncaoObra = new Select();
$obCmbTipoFuncaoObra->setName       ( "inCodTipoFuncaoObra"                 );
$obCmbTipoFuncaoObra->setRotulo     ( "Função"                              );
$obCmbTipoFuncaoObra->setId         ( "inCodTipoFuncaoObra"                 );
$obCmbTipoFuncaoObra->setCampoId    ( "cod_funcao"                          );
$obCmbTipoFuncaoObra->setCampoDesc  ( "[nro_funcao] - [descricao]"          );
$obCmbTipoFuncaoObra->addOption     ( '','Selecione'                        );
$obCmbTipoFuncaoObra->preencheCombo ( $rsTipoFuncaoObra                     );
$obCmbTipoFuncaoObra->setNull       ( false                                 );
$obCmbTipoFuncaoObra->setValue      ( $request->get('inCodTipoFuncaoObra')  );

$obTxtNroObra = new TextBox;
$obTxtNroObra->setName     ( "stNroObra"                  );
$obTxtNroObra->setId       ( "stNroObra"                  );
$obTxtNroObra->setValue    ( $request->get('stNroObra')   );
$obTxtNroObra->setRotulo   ( "Número da Obra"             );
$obTxtNroObra->setTitle    ( "Informe o número da Obra."  );
$obTxtNroObra->setNull     ( false                        );
$obTxtNroObra->setSize     ( 21                           );
$obTxtNroObra->setMaxLength( 10                           );

$obTxtAreaDescricao = new TextArea();
$obTxtAreaDescricao->setName            ( 'stDescricao'                 );
$obTxtAreaDescricao->setId              ( 'stDescricao'                 );
$obTxtAreaDescricao->setRotulo          ( 'Descrição'                   );
$obTxtAreaDescricao->setTitle           ( 'Descrição da obra.'          );
$obTxtAreaDescricao->setMaxCaracteres   ( 255                           );
$obTxtAreaDescricao->setRows            ( 2                             );
$obTxtAreaDescricao->setNull            ( false                         );
$obTxtAreaDescricao->setValue           ( $request->get('stDescricao')  );

$obNumVlObra = new Moeda;
$obNumVlObra->setId         ( 'nuVlObra'                );
$obNumVlObra->setName       ( 'nuVlObra'                );
$obNumVlObra->setRotulo     ( 'Valor da Obra'           );
$obNumVlObra->setAlign      ( 'RIGHT'                   );
$obNumVlObra->setMaxLength  ( 21                        );
$obNumVlObra->setSize       ( 21                        );
$obNumVlObra->setValue      ( $request->get('nuVlObra') );
$obNumVlObra->setNull       ( false                     );

$obDtCadastro = new Data;
$obDtCadastro->setName      ( "dtCadastro"                          );
$obDtCadastro->setId        ( "dtCadastro"                          );
$obDtCadastro->setRotulo    ( "Data de Cadastro"                    );
$obDtCadastro->setValue     ( $request->get('dtCadastro')           );
$obDtCadastro->setTitle     ( 'Informe a data de cadastro da obra.' );
$obDtCadastro->setNull      ( false                                 );
$obDtCadastro->setSize      ( 10                                    );
$obDtCadastro->setMaxLength ( 10                                    );

$obDtInicio = new Data;
$obDtInicio->setName        ( "dtInicio"                            );
$obDtInicio->setId          ( "dtInicio"                            );
$obDtInicio->setRotulo      ( "Data de Início"                      );
$obDtInicio->setValue       ( $request->get('dtInicio')             );
$obDtInicio->setTitle       ( 'Informe a data de início da obra.'   );
$obDtInicio->setNull        ( false                                 );
$obDtInicio->setSize        ( 10                                    );
$obDtInicio->setMaxLength   ( 10                                    );

$obDtAceite = new Data;
$obDtAceite->setName        ( "dtAceite"                            );
$obDtAceite->setId          ( "dtAceite"                            );
$obDtAceite->setRotulo      ( "Data de Aceite"                      );
$obDtAceite->setValue       ( $request->get('dtAceite')             );
$obDtAceite->setTitle       ( 'Informe a data de aceite da obra.'   );
$obDtAceite->setNull        ( false                                 );
$obDtAceite->setSize        ( 10                                    );
$obDtAceite->setMaxLength   ( 10                                    );

$obDtPrazo = new Data;
$obDtPrazo->setName         ( "dtPrazo"                                 );
$obDtPrazo->setId           ( "dtPrazo"                                 );
$obDtPrazo->setRotulo       ( "Prazo de conclusão"                      );
$obDtPrazo->setValue        ( $request->get('dtPrazo')                  );
$obDtPrazo->setTitle        ( 'Informe o prazo de conclusão da obra.'   );
$obDtPrazo->setNull         ( false                                     );
$obDtPrazo->setSize         ( 10                                        );
$obDtPrazo->setMaxLength    ( 10                                        );

$obDtRecebimento = new Data;
$obDtRecebimento->setName       ( "dtRecebimento"                                       );
$obDtRecebimento->setId         ( "dtRecebimento"                                       );
$obDtRecebimento->setRotulo     ( "Data recebimento definitivo"                         );
$obDtRecebimento->setValue      ( $request->get('dtRecebimento')                        );
$obDtRecebimento->setTitle      ( 'Informe a data de recebimento definitivo da obra.'   );
$obDtRecebimento->setNull       ( false                                                 );
$obDtRecebimento->setSize       ( 10                                                    );
$obDtRecebimento->setMaxLength  ( 10                                                    );

//Montando Licitação Urbem
$obTxtExercicioLicitacao = new TextBox();
$obTxtExercicioLicitacao->setName       ( 'stExercicioLicitacao'                                        );
$obTxtExercicioLicitacao->setId         ( 'stExercicioLicitacao'                                        );
$obTxtExercicioLicitacao->setRotulo     ( '**Exercício Licitação'                                       );
$obTxtExercicioLicitacao->setMaxLength  ( 4                                                             );
$obTxtExercicioLicitacao->setSize       ( 5                                                             );
$obTxtExercicioLicitacao->setNull       ( true                                                          );
$obTxtExercicioLicitacao->setValue      ( $request->get('stExercicioLicitacao', Sessao::getExercicio()) );
$obTxtExercicioLicitacao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");

$obISelectModalidade = new Select();
$obISelectModalidade->setName       ( 'inCodModalidade'                         );
$obISelectModalidade->setId         ( 'inCodModalidade'                         );
$obISelectModalidade->setRotulo     ( '**Modalidade Licitação'                  );
$obISelectModalidade->setTitle      ( 'Selecione a Modalidade da Licitação.'    );
$obISelectModalidade->setCampoID    ( 'cod_modalidade'                          );
$obISelectModalidade->setValue      ( $request->get('inCodModalidade')          );
$obISelectModalidade->setCampoDesc  ( '[cod_modalidade] - [descricao]'          );
$obISelectModalidade->addOption     ( '','Selecione'                            );
$obISelectModalidade->setNull       ( true                                      );
$obISelectModalidade->preencheCombo ( $rsModalidade                             );
$obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");

$obISelectLicitacao = new Select();
$obISelectLicitacao->setName    ( 'inCodLicitacao'                  );
$obISelectLicitacao->setId      ( 'inCodLicitacao'                  );
$obISelectLicitacao->setRotulo  ( '**Licitação'                     );
$obISelectLicitacao->setTitle   ( 'Selecione a Licitação.'          );
$obISelectLicitacao->addOption  ( '','Selecione'                    );
$obISelectLicitacao->setNull    ( true                              );
$obISelectLicitacao->setValue   ( $request->get('inCodLicitacao')   );
$obISelectLicitacao->setCampoID    ( 'cod_licitacao'                );
$obISelectLicitacao->setCampoDesc  ( 'cod_licitacao'                );
$obISelectLicitacao->preencheCombo ( $rsLicitacao                   );

#Andamento da obra

$obCmbSituacaoObra = new Select();
$obCmbSituacaoObra->setName         ( "inSituacaoObra"                  );
$obCmbSituacaoObra->setRotulo       ( "**Situação"                      );
$obCmbSituacaoObra->setId           ( "inSituacaoObra"                  );
$obCmbSituacaoObra->setCampoId      ( "cod_situacao"                    );
$obCmbSituacaoObra->setCampoDesc    ( "descricao"                       );
$obCmbSituacaoObra->addOption       ( '','Selecione'                    );
$obCmbSituacaoObra->preencheCombo   ( $rsSituacaoObra                   );
$obCmbSituacaoObra->setNull         ( true                              );
$obCmbSituacaoObra->setValue        ( $request->get('inSituacaoObra')   );
$obCmbSituacaoObra->obEvento->setOnChange("montaParametrosGET('montaJustificativa');");

$obDtSituacao = new Data;
$obDtSituacao->setName      ( "dtSituacao"                          );
$obDtSituacao->setId        ( "dtSituacao"                          );
$obDtSituacao->setRotulo    ( "**Data da Situação"                  );
$obDtSituacao->setValue     ( $request->get('dtSituacao')           );
$obDtSituacao->setTitle     ( 'Informe a data da situação da obra.' );
$obDtSituacao->setNull      ( true                                  );
$obDtSituacao->setSize      ( 10                                    );
$obDtSituacao->setMaxLength ( 10                                    );

$obTxtAreaJustificativa = new TextArea();
$obTxtAreaJustificativa->setName            ( 'stJustificativa'                     );
$obTxtAreaJustificativa->setId              ( 'stJustificativa'                     );
$obTxtAreaJustificativa->setRotulo          ( '**Justificativa'                     );
$obTxtAreaJustificativa->setTitle           ( 'Justificativa da situação da obra.'  );
$obTxtAreaJustificativa->setMaxCaracteres   ( 255                                   );
$obTxtAreaJustificativa->setRows            ( 2                                     );
$obTxtAreaJustificativa->setNull            ( true                                  );
$obTxtAreaJustificativa->setValue           ( $request->get('stJustificativa')      );
$obTxtAreaJustificativa->setDisabled        ( true                                  );

$obBtnIncluirAndamento = new Button;
$obBtnIncluirAndamento->setValue             ( "Incluir"                                    );
$obBtnIncluirAndamento->setName              ( "btnIncluirAndamento"                        );
$obBtnIncluirAndamento->setId                ( "btnIncluirAndamento"                        );
$obBtnIncluirAndamento->obEvento->setOnClick ( "montaParametrosGET('incluirAndamento');"    );

$obBtnLimparAndamento = new Button;
$obBtnLimparAndamento->setName              ( "btnLimparAndamento"                      );
$obBtnLimparAndamento->setId                ( "limparAndamento"                         );
$obBtnLimparAndamento->setValue             ( "Limpar"                                  );
$obBtnLimparAndamento->obEvento->setOnClick ( "montaParametrosGET('limparAndamento');"  );

$spnListaAndamento = new Span;
$spnListaAndamento->setId ( 'spnListaAndamento' );

#Fiscais de Obras

$obBscResponsavelFiscal = new BuscaInner;
$obBscResponsavelFiscal->setRotulo                      ( "**CGM do Responsável Fiscal"                 );
$obBscResponsavelFiscal->setTitle                       ( "Informe o código CGM do Responsável Fiscal"  );
$obBscResponsavelFiscal->setNull                        ( true                                          );
$obBscResponsavelFiscal->setId                          ( "inNomResponsavelFiscal"                      );
$obBscResponsavelFiscal->setValue                       ( $request->get('stNomResponsavelFiscal')       );
$obBscResponsavelFiscal->obCampoCod->setName            ( "inNumResponsavelFiscal"                      );
$obBscResponsavelFiscal->obCampoCod->setId              ( "inNumResponsavelFiscal"                      );
$obBscResponsavelFiscal->obCampoCod->setValue           ( $request->get('inNumResponsavelFiscal')       );
$obBscResponsavelFiscal->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+frm.inCodEntidade.value+'&inNumResponsavelFiscal='+this.value,'buscaResponsavelFiscal');" );
$obBscResponsavelFiscal->setFuncaoBusca                 ( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumResponsavelFiscal','inNomResponsavelFiscal','','".Sessao::getId()."','800','550');" );

$obTxtMatricula = new TextBox();
$obTxtMatricula->setName        ( 'stMatricula'                 );
$obTxtMatricula->setId          ( 'stMatricula'                 );
$obTxtMatricula->setRotulo      ( 'Matrícula'                   );
$obTxtMatricula->setMaxLength   ( 10                            );
$obTxtMatricula->setSize        ( 10                            );
$obTxtMatricula->setValue       ( $request->get('stMatricula')  );
$obTxtMatricula->setNull        ( true                          );

$obTxtRegistro = new TextBox();
$obTxtRegistro->setName     ( 'stRegistro'                  );
$obTxtRegistro->setId       ( 'stRegistro'                  );
$obTxtRegistro->setRotulo   ( 'Registro Profissional'       );
$obTxtRegistro->setMaxLength( 10                            );
$obTxtRegistro->setSize     ( 10                            );
$obTxtRegistro->setValue    ( $request->get('stRegistro')   );
$obTxtRegistro->setNull     ( true                          );
$obTxtRegistro->setInteiro  ( true                          );

$obDtInicioFiscal = new Data;
$obDtInicioFiscal->setName      ( "dtInicioFiscal"                  );
$obDtInicioFiscal->setId        ( "dtInicioFiscal"                  );
$obDtInicioFiscal->setRotulo    ( "**Data de Início"                );
$obDtInicioFiscal->setValue     ( $request->get('dtInicioFiscal')   );
$obDtInicioFiscal->setNull      ( true                              );
$obDtInicioFiscal->setSize      ( 10                                );
$obDtInicioFiscal->setMaxLength ( 10                                );

$obDtFinalFiscal = new Data;
$obDtFinalFiscal->setName       ( "dtFinalFiscal"                           );
$obDtFinalFiscal->setId         ( "dtFinalFiscal"                           );
$obDtFinalFiscal->setRotulo     ( "**Data Final"                            );
$obDtFinalFiscal->setValue      ( $request->get('dtFinalFiscal')            );
$obDtFinalFiscal->setNull       ( true                                      );
$obDtFinalFiscal->setSize       ( 10                                        );
$obDtFinalFiscal->setMaxLength  ( 10                                        );

$obBtnIncluirFiscal = new Button;
$obBtnIncluirFiscal->setValue               ( "Incluir"                                 );
$obBtnIncluirFiscal->setName                ( "btnIncluirFiscal"                        );
$obBtnIncluirFiscal->setId                  ( "btnIncluirFiscal"                        );
$obBtnIncluirFiscal->obEvento->setOnClick   ( "montaParametrosGET('incluirFiscal');"    );

$obBtnLimparFiscal = new Button;
$obBtnLimparFiscal->setName             ( "btnLimparFiscal"                     );
$obBtnLimparFiscal->setId               ( "limparFiscal"                        );
$obBtnLimparFiscal->setValue            ( "Limpar"                              );
$obBtnLimparFiscal->obEvento->setOnClick( "montaParametrosGET('limparFiscal');" );

$spnListaFiscal = new Span;
$spnListaFiscal->setId ( 'spnListaFiscal' );

#Medições de Obras

$obTxtNroMedicao = new TextBox();
$obTxtNroMedicao->setName       ( 'inNroMedicao'                );
$obTxtNroMedicao->setId         ( 'inNroMedicao'                );
$obTxtNroMedicao->setRotulo     ( '**Número'                    );
$obTxtNroMedicao->setMaxLength  ( 10                            );
$obTxtNroMedicao->setSize       ( 10                            );
$obTxtNroMedicao->setValue      ( $request->get('inNroMedicao') );
$obTxtNroMedicao->setNull       ( true                          );
$obTxtNroMedicao->setInteiro    ( true                          );

$obISelectMedidaObra = new Select();
$obISelectMedidaObra->setName       ( 'inCodMedidaObra'                         );
$obISelectMedidaObra->setId         ( 'inCodMedidaObra'                         );
$obISelectMedidaObra->setRotulo     ( '**Unidade de Medida da Obra'             );
$obISelectMedidaObra->setTitle      ( 'Selecione a Unidade de Medida da Obra.'  );
$obISelectMedidaObra->setCampoID    ( 'cod_medida'                              );
$obISelectMedidaObra->setValue      ( $request->get('inCodMedidaObra')          );
$obISelectMedidaObra->setCampoDesc  ( 'descricao'                               );
$obISelectMedidaObra->addOption     ( '','Selecione'                            );
$obISelectMedidaObra->setNull       ( true                                      );
$obISelectMedidaObra->preencheCombo ( $rsMedidasObra                            );

$obDtInicioMedicao = new Data;
$obDtInicioMedicao->setName     ( "dtInicioMedicao"                 );
$obDtInicioMedicao->setId       ( "dtInicioMedicao"                 );
$obDtInicioMedicao->setRotulo   ( "**Data de Início"                );
$obDtInicioMedicao->setValue    ( $request->get('dtInicioMedicao')  );
$obDtInicioMedicao->setNull     ( true                              );
$obDtInicioMedicao->setSize     ( 10                                );
$obDtInicioMedicao->setMaxLength( 10                                );

$obDtFinalMedicao = new Data;
$obDtFinalMedicao->setName      ( "dtFinalMedicao"                  );
$obDtFinalMedicao->setId        ( "dtFinalMedicao"                  );
$obDtFinalMedicao->setRotulo    ( "**Data Final"                    );
$obDtFinalMedicao->setValue     ( $request->get('dtFinalMedicao')   );
$obDtFinalMedicao->setNull      ( true                              );
$obDtFinalMedicao->setSize      ( 10                                );
$obDtFinalMedicao->setMaxLength ( 10                                );

$obDtMedicao = new Data;
$obDtMedicao->setId        ( "dtMedicao"                       );
$obDtMedicao->setName      ( "dtMedicao"                       );
$obDtMedicao->setRotulo    ( "**Data da Medição"               );
$obDtMedicao->setValue     ( $request->get('dtMedicao')        );
$obDtMedicao->setNull      ( true                              );
$obDtMedicao->setSize      ( 10                                );
$obDtMedicao->setMaxLength ( 10                                );

$obNumVlMedicao = new Moeda;
$obNumVlMedicao->setId          ( 'nuVlMedicao'                 );
$obNumVlMedicao->setName        ( 'nuVlMedicao'                 );
$obNumVlMedicao->setRotulo      ( '**Valor da Medição'          );
$obNumVlMedicao->setAlign       ( 'RIGHT'                       );
$obNumVlMedicao->setMaxLength   ( 21                            );
$obNumVlMedicao->setSize        ( 21                            );
$obNumVlMedicao->setValue       ( $request->get('nuVlMedicao')  );
$obNumVlMedicao->setNull        ( true                          );

$obTxtNFMedicao = new TextBox();
$obTxtNFMedicao->setName        ( 'stNFMedicao'                 );
$obTxtNFMedicao->setId          ( 'stNFMedicao'                 );
$obTxtNFMedicao->setRotulo      ( '**Número da Nota Fiscal'     );
$obTxtNFMedicao->setMaxLength   ( 20                            );
$obTxtNFMedicao->setSize        ( 21                            );
$obTxtNFMedicao->setValue       ( $request->get('stNFMedicao')  );
$obTxtNFMedicao->setNull        ( true                          );

$obDtNFMedicao = new Data;
$obDtNFMedicao->setName     ( "dtNFMedicao"                 );
$obDtNFMedicao->setId       ( "dtNFMedicao"                 );
$obDtNFMedicao->setRotulo   ( "**Data da Nota Fiscal"       );
$obDtNFMedicao->setValue    ( $request->get('dtNFMedicao')  );
$obDtNFMedicao->setNull     ( true                          );
$obDtNFMedicao->setSize     ( 10                            );
$obDtNFMedicao->setMaxLength( 10                            );

$obBscAtestadorMedicao = new BuscaInner;
$obBscAtestadorMedicao->setRotulo                       ( "**CGM do Atestador da Medição"                   );
$obBscAtestadorMedicao->setTitle                        ( "Informe o código CGM do Atestador da Medição"    );
$obBscAtestadorMedicao->setNull                         ( true                                              );
$obBscAtestadorMedicao->setId                           ( "inNomAtestadorMedicao"                           );
$obBscAtestadorMedicao->setValue                        ( $request->get('stNomAtestadorMedicao')            );
$obBscAtestadorMedicao->obCampoCod->setName             ( "inNumAtestadorMedicao"                           );
$obBscAtestadorMedicao->obCampoCod->setId               ( "inNumAtestadorMedicao"                           );
$obBscAtestadorMedicao->obCampoCod->setValue            ( $request->get('inNumAtestadorMedicao')            );
$obBscAtestadorMedicao->obCampoCod->obEvento->setOnBlur ( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumAtestadorMedicao='+this.value,'buscaAtestadorMedicao');" );
$obBscAtestadorMedicao->setFuncaoBusca                  ( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAtestadorMedicao','inNomAtestadorMedicao','fisica','".Sessao::getId()."','800','550');" );

$obBtnIncluirMedicao = new Button;
$obBtnIncluirMedicao->setValue              ( "Incluir"                                 );
$obBtnIncluirMedicao->setName               ( "btnIncluirMedicao"                       );
$obBtnIncluirMedicao->setId                 ( "btnIncluirMedicao"                       );
$obBtnIncluirMedicao->obEvento->setOnClick  ( "montaParametrosGET('incluirMedicao');"   );

$obBtnLimparMedicao = new Button;
$obBtnLimparMedicao->setName                ( "btnLimparMedicao"                        );
$obBtnLimparMedicao->setId                  ( "limparMedicao"                           );
$obBtnLimparMedicao->setValue               ( "Limpar"                                  );
$obBtnLimparMedicao->obEvento->setOnClick   ( "montaParametrosGET('limparMedicao');"    );

$spnListaMedicao = new Span;
$spnListaMedicao->setId ( 'spnListaMedicao' );

#Contratos de Obras

$obISelectTipoContratacao = new Select();
$obISelectTipoContratacao->setName      ( 'inCodTipoContratacao'                );
$obISelectTipoContratacao->setId        ( 'inCodTipoContratacao'                );
$obISelectTipoContratacao->setRotulo    ( '**Tipo de Contratação'               );
$obISelectTipoContratacao->setTitle     ( 'Selecione o Tipo de Contratação.'    );
$obISelectTipoContratacao->setCampoID   ( 'cod_contratacao'                     );
$obISelectTipoContratacao->setValue     ( $request->get('inCodTipoContratacao') );
$obISelectTipoContratacao->setCampoDesc ( 'descricao'                           );
$obISelectTipoContratacao->addOption    ( '','Selecione'                        );
$obISelectTipoContratacao->setNull      ( true                                  );
$obISelectTipoContratacao->preencheCombo( $rsTipoContratacao                    );
$obISelectTipoContratacao->obEvento->setOnChange( "montaParametrosGET('montaNroTipoContratacao');" );

$spnNroTipoContratacao = new Span;
$spnNroTipoContratacao->setId ( 'spnNroTipoContratacao' );

$obBscContratado = new BuscaInner;
$obBscContratado->setRotulo                         ( "**CGM do Contratado"                 );
$obBscContratado->setTitle                          ( "Informe o código CGM do Contratado"  );
$obBscContratado->setNull                           ( true                                  );
$obBscContratado->setId                             ( "inNomContratado"                     );
$obBscContratado->setValue                          ( $request->get('inNomContratado')      );
$obBscContratado->obCampoCod->setName               ( "inNumContratado"                     );
$obBscContratado->obCampoCod->setId                 ( "inNumContratado"                     );
$obBscContratado->obCampoCod->setValue              ( $request->get('inNumContratado')      );
$obBscContratado->obCampoCod->obEvento->setOnBlur   ( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumContratado='+this.value,'buscaContratado');" );
$obBscContratado->setFuncaoBusca                    ( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumContratado','inNomContratado','fisica','".Sessao::getId()."','800','550');" );

$obTxtFuncaoContratada = new TextBox();
$obTxtFuncaoContratada->setName     ( 'stFuncaoContratada'                  );
$obTxtFuncaoContratada->setId       ( 'stFuncaoContratada'                  );
$obTxtFuncaoContratada->setRotulo   ( '**Função Contratada'                 );
$obTxtFuncaoContratada->setMaxLength( 50                                    );
$obTxtFuncaoContratada->setSize     ( 40                                    );
$obTxtFuncaoContratada->setValue    ( $request->get('stFuncaoContratada')   );
$obTxtFuncaoContratada->setNull     ( true                                  );

$obDtInicioContrato = new Data;
$obDtInicioContrato->setName        ( "dtInicioContrato"                    );
$obDtInicioContrato->setId          ( "dtInicioContrato"                    );
$obDtInicioContrato->setRotulo      ( "**Data de Início"                    );
$obDtInicioContrato->setValue       ( $request->get('dtInicioContrato')     );
$obDtInicioContrato->setNull        ( true                                  );
$obDtInicioContrato->setSize        ( 10                                    );
$obDtInicioContrato->setMaxLength   ( 10                                    );

$obDtFinalContrato = new Data;
$obDtFinalContrato->setName         ( "dtFinalContrato"                     );
$obDtFinalContrato->setId           ( "dtFinalContrato"                     );
$obDtFinalContrato->setRotulo       ( "**Data Final"                        );
$obDtFinalContrato->setValue        ( $request->get('dtFinalContrato')      );
$obDtFinalContrato->setNull         ( true                                  );
$obDtFinalContrato->setSize         ( 10                                    );
$obDtFinalContrato->setMaxLength    ( 10                                    );

$obTxtLotacao = new TextBox();
$obTxtLotacao->setName      ( 'stLotacao'                                   );
$obTxtLotacao->setId        ( 'stLotacao'                                   );
$obTxtLotacao->setRotulo    ( 'Lotação'                                     );
$obTxtLotacao->setMaxLength ( 50                                            );
$obTxtLotacao->setSize      ( 40                                            );
$obTxtLotacao->setValue     ( $request->get('stLotacao')                    );
$obTxtLotacao->setNull      ( true                                          );

$obBtnIncluirContrato = new Button;
$obBtnIncluirContrato->setValue             ( "Incluir"                                 );
$obBtnIncluirContrato->setName              ( "btnIncluirContrato"                      );
$obBtnIncluirContrato->setId                ( "btnIncluirContrato"                      );
$obBtnIncluirContrato->obEvento->setOnClick ( "montaParametrosGET('incluirContrato');"  );

$obBtnLimparContrato = new Button;
$obBtnLimparContrato->setName               ( "btnLimparContrato"                       );
$obBtnLimparContrato->setId                 ( "limparContrato"                          );
$obBtnLimparContrato->setValue              ( "Limpar"                                  );
$obBtnLimparContrato->obEvento->setOnClick  ( "montaParametrosGET('limparContrato');"   );

$spnListaContrato = new Span;
$spnListaContrato->setId ( 'spnListaContrato' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo( "Dados de Obras e Serviços de Engenharia" );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnCodObra );
$obFormulario->addHidden        ( $obHdnExercicio );
$obFormulario->addHidden        ( $obHdnIdAndamento );
$obFormulario->addHidden        ( $obHdnIdFiscal );
$obFormulario->addHidden        ( $obHdnIdMedicao );
$obFormulario->addHidden        ( $obHdnIdContrato );
$obFormulario->addComponente    ( $obEntidade );
$obFormulario->addComponente    ( $obCmbTipoObra );
$obFormulario->addComponente    ( $obTxtLocal );
$obFormulario->addComponente    ( $obTxtCEP );
$obFormulario->addComponente    ( $obCmbBairro );
$obFormulario->addComponente    ( $obCmbTipoFuncaoObra );
$obFormulario->addComponente    ( $obTxtNroObra );
$obFormulario->addComponente    ( $obTxtAreaDescricao );
$obFormulario->addComponente    ( $obNumVlObra );
$obFormulario->addComponente    ( $obDtCadastro );
$obFormulario->addComponente    ( $obDtInicio );
$obFormulario->addComponente    ( $obDtAceite );
$obFormulario->addComponente    ( $obDtPrazo );
$obFormulario->addComponente    ( $obDtRecebimento );
$obFormulario->addComponente    ( $obTxtExercicioLicitacao );
$obFormulario->addComponente    ( $obISelectModalidade );
$obFormulario->addComponente    ( $obISelectLicitacao );

$obFormulario->addTitulo( "Andamento da obra" );
$obFormulario->addComponente    ( $obCmbSituacaoObra );
$obFormulario->addComponente    ( $obDtSituacao );
$obFormulario->addComponente    ( $obTxtAreaJustificativa );
$obFormulario->agrupaComponentes( array( $obBtnIncluirAndamento, $obBtnLimparAndamento ),"","" );
$obFormulario->addSpan          ( $spnListaAndamento );

$obFormulario->addTitulo( "Fiscais de Obras e Serviços de Engenharia" );
$obFormulario->addComponente    ( $obBscResponsavelFiscal );
$obFormulario->addComponente    ( $obTxtMatricula );
$obFormulario->addComponente    ( $obTxtRegistro );
$obFormulario->addComponente    ( $obDtInicioFiscal );
$obFormulario->addComponente    ( $obDtFinalFiscal );
$obFormulario->agrupaComponentes( array( $obBtnIncluirFiscal, $obBtnLimparFiscal ),"","" );
$obFormulario->addSpan          ( $spnListaFiscal );

$obFormulario->addTitulo( "Medições de Obras e Serviços de Engenharia" );
$obFormulario->addComponente    ( $obTxtNroMedicao );
$obFormulario->addComponente    ( $obISelectMedidaObra );
$obFormulario->addComponente    ( $obDtInicioMedicao );
$obFormulario->addComponente    ( $obDtFinalMedicao );
$obFormulario->addComponente    ( $obDtMedicao );
$obFormulario->addComponente    ( $obNumVlMedicao );
$obFormulario->addComponente    ( $obTxtNFMedicao );
$obFormulario->addComponente    ( $obDtNFMedicao );
$obFormulario->addComponente    ( $obBscAtestadorMedicao );
$obFormulario->agrupaComponentes( array( $obBtnIncluirMedicao, $obBtnLimparMedicao ),"","" );
$obFormulario->addSpan          ( $spnListaMedicao );

$obFormulario->addTitulo( "Contratos de Mão de Obra e Serviços de Engenharia" );
$obFormulario->addComponente    ( $obISelectTipoContratacao );
$obFormulario->addSpan          ( $spnNroTipoContratacao );
$obFormulario->addComponente    ( $obBscContratado );
$obFormulario->addComponente    ( $obTxtFuncaoContratada );
$obFormulario->addComponente    ( $obDtInicioContrato );
$obFormulario->addComponente    ( $obDtFinalContrato );
$obFormulario->addComponente    ( $obTxtLotacao );
$obFormulario->agrupaComponentes( array( $obBtnIncluirContrato, $obBtnLimparContrato ),"","" );
$obFormulario->addSpan          ( $spnListaContrato );

$obOk = new Ok(true);

$obLimpar = new Limpar;
$obLimpar->obEvento->setOnClick( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."', 'LimparForm');");

$obCancelar  = new Cancelar();
$obCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().$stLink."','telaPrincipal');");

if ($stAcao == 'alterar') {
    $obFormulario->defineBarra( array( $obOk, $obCancelar ) );
}else{
    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>