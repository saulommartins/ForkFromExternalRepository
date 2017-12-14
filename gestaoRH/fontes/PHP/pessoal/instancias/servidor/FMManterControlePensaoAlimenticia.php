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
* Página de Formulário para controle de pensão alimenticia
* Data de criação : 04/04/2006

* @author Analista: Vadré Miguel Ramos
* @author Programador: Bruce Cruz de Sena

* @ignore

* Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalDependente.class.php'                                      );
include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalPensao.class.php'                                          );
include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalServidor.class.php'                                        );

Sessao::remove('aPensoes');
Sessao::remove('arPensoesExcluidas');

//Define o nome dos arquivos PHP
$stPrograma = 'ManterControlePensaoAlimenticia';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ( $pgJs   );
include_once ( $pgOcul );

function criaLabel($nome, $valor)
{
    $ret = new Label;
    $ret->setName   ( $nome  );
    $ret->setValue  ( $valor );

    return $ret;
}

function criaCheckBox($nome, $rotulo, $titulo)
{
    $retChk = new CheckBox;
    $retChk->setName    ( $nome   );
    $retChk->setId      ( $nome   );
    $retChk->setRotulo  ( $rotulo );
    $retChk->setTitle   ( $titulo );
    $retChk->setValue   ( 't'     );
    $retChk->setChecked ( false   );

    return $retChk;
}

function adicionaRegistros(&$rsPensoes, &$obRDependente, &$obRPessoalPensao,Request $request)
{
    $arRegistro = array();

    Sessao::write('aPensoes',array());

    while ( !$rsPensoes->eof() ) {
        $arPensoes = Sessao::read("aPensoes");
        $arRegistro = array();
        $arRegistro[ 'cod_pensao'      ] = $rsPensoes->getCampo( 'cod_pensao'     );
        $arRegistro[ 'timeStamp'       ] = $rsPensoes->getCampo( 'timestamp'      );
        $arRegistro[ 'cod_dependente'  ] = $rsPensoes->getCampo( 'cod_dependente' );
        //nome do dependente
        $obRDependente->setCodDependente        ( $rsPensoes->getCampo( 'cod_dependente' ) );
        $obRDependente->consultarDependente     ( $rsDeps                                  );
        $arRegistro[ 'inId'            ] = count ( $arPensoes );
        $arRegistro[ 'numcgm'          ] = $rsDeps->getCampo('numcgm')   ;
        $arRegistro[ 'dependente'      ] = $rsDeps->getCampo('nom_cgm')   ;
        $arRegistro[ 'tipo_pensao'     ] = ($rsPensoes->getCampo('tipo_pensao') == 'J') ? 'Jurídica' : 'Amigavel';
        $arRegistro[ 'OBS'             ] = $rsPensoes->getCampo('observacao'    );
        $arRegistro[ 'Percentual'      ] = $rsPensoes->getCampo('percentual'    );
        $arRegistro[ 'dataInclusao'    ] = $rsPensoes->getCampo('dt_inclusao'   );
        $arRegistro[ 'dataLimite'      ] = $rsPensoes->getCampo('dt_limite'     );

        $arRegistro[ 'codBanco'        ] = $rsPensoes->getCampo('num_banco'     );
        $arRegistro[ 'codAgencia'      ] = $rsPensoes->getCampo('num_agencia'   );

        $arRegistro[ 'contaCorrente'   ] = $rsPensoes->getCampo('conta_corrente');
        // dados de função
        $arRegistro[ 'evento'          ] = $rsPensoes->getCampo('nom_funcao'    );
        $arRegistro[ 'Funcao'          ] = $rsPensoes->getCampo('nom_funcao'    );
        $arRegistro[ 'codFuncao'       ] = $rsPensoes->getCampo('cod_modulo')    .'.'.
                                           $rsPensoes->getCampo('cod_biblioteca').'.'.
                                           $rsPensoes->getCampo('cod_funcao');
        $arRegistro[ 'valor'           ] = ($rsPensoes->getCampo('valor') > 0) ? $rsPensoes->getCampo('valor') : "";
        $arRegistro[ 'inNumCGMResp'    ] = $rsPensoes->getCampo('numcgm');
        // incidencias
        $rsIncidencias = new RecordSet;

        $obRPessoalPensao->listarIncidenciasPensao( $rsIncidencias,
                                                    $rsPensoes->getCampo('cod_pensao'),
                                                    $rsPensoes->getCampo('timestamp' ) );
        while ( !$rsIncidencias->eof() ) {
            $arRegistro[ 'chIncidencia_'.$rsIncidencias->getCampo('cod_incidencia') ] = true;
            $rsIncidencias->proximo();
        }
        $rsPensoes->proximo();
        $arPensoes[] = $arRegistro;        
        Sessao::write("aPensoes",$arPensoes);
    }
}

$stAcao = $request->get('stAcao');
if ($request->get('inNumCGM')) {
    $inCGM = $request->get('inNumCGM');
} else {
    // retiranto o cgm da strimg  $_POST['hdnCGM']
    $inCGM = $request->get('hdnCGM');
    $inCGM = trim( substr( $inCGM, 0, ( strpos($inCGM, '-') -1)) );
}

$rsServidor = new recordSet;
$obRPessoalServidor = new RPessoalServidor;
$obRPessoalServidor->obRCGMPessoaFisica->setNumCGM ( $inCGM                                 );
$obRPessoalServidor->consultarServidor             ( $rsServidor, false                     );
$obRPessoalServidor->setCodServidor                ( $rsServidor->getCampo( 'cod_servidor') );
$obRPessoalServidor->obRCGMPessoaFisica->consultarCGM( $rsCGM );

$stServidor = $inCGM . ' - '. $rsCGM->getCampo( 'nom_cgm' ) ;

$obRDependente  = new RPessoalDependente ($obRPessoalServidor) ;

/// verificando se o servidor tem dependentes
$obRDependente->listarPessoalDependente ( $rsDependentes );

$obForm = new Form;
$obForm->setAction ( $pgProc  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );

$obHdnCGM =  new Hidden;
$obHdnCGM->setName  ( 'inCGM' );
$obHdnCGM->setValue ( $inCGM  );
$obFormulario->addHidden ( $obHdnCGM );

$obHdnEval = new HiddenEval;
$obHdnEval->setName ( "stEval" );
$obHdnEval->setValue( "" );

$obLblCGM = new Label;
$obLblCGM->setName   ( 'inCGM'           );
$obLblCGM->setId     ( 'inCGM'           );
$obLblCGM->setRotulo ( 'CGM'             );
$obLblCGM->setValue  ( $stServidor       ); // $_POST['hdnCGM']  );

$obLblContrato = new Label;
$obLblContrato->setRotulo ( 'Matrícula' );
$obLblContrato->setValue  ( $request->get('inContrato') );

if ( $rsDependentes->getNumLinhas() <= 0 ) {
    ///servidor sem dependentes
    $obFormulario->addTitulo('Informações da Matrícula');

    $obLblErro = new Label;
    $obLblErro->setRotulo ( 'Observação' );
    $obLblErro->setValue  ( 'A Matrícula selecionado não possui dependentes cadastrados.' );

    $obLnkAbaDependentes = new Link;
    $obLnkAbaDependentes->setRotulo ( 'Link'                                    );

    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->listarContratos($rsContrato);

    $obLnkAbaDependentes->setHref   ( 'JavaScript:abreAbaDependentes('. $rsContrato->getCampo('numcgm')  .' , '.
                                                                        $rsContrato->getCampo('cod_contrato')   .' , '.
                                                                        $rsContrato->getCampo('cod_servidor') . ' );' );

    $obLnkAbaDependentes->setValue  ( "Cadastrar Dependentes"                   );

    $obFormulario->addComponente ( $obLblErro       );

    $obFormulario->addComponente ( $obLblCGM        );
    $obFormulario->addComponente ( $obLblContrato   );

    $obFormulario->addComponente ( $obLnkAbaDependentes );

    $obFormulario->show();

} else {
    $obRPessoalPensao = new RPessoalPensao;

    $rsPensoes = new RecordSet;
    $arFiltro = array();
    $arFiltro[0]['campo']    = 'pensao.cod_servidor';
    $arFiltro[0]['condicao'] = '=';
    $arFiltro[0]['valor']    = $obRPessoalServidor->getCodServidor();

    $obRPessoalPensao->listarFiltro($rsPensoes, $arFiltro );
    $rsPensoes->addFormatacao( 'valor', 'NUMERIC_BR' );
    adicionaRegistros($rsPensoes, $obRDependente, $obRPessoalPensao,$request);

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName     ( 'stCtrl' );
    $obHdnCtrl->setValue    ( ''       );

    $obHdnRegistroAtual = new Hidden;
    $obHdnRegistroAtual->setName ( 'inCodRegAtual' );

    // dados de dependentes devem ser listados apenas os dependentes não
    // excluidos e que não estejam na listagem "Dependentes com direito a pensão"
    $obTxtCodDependente = new TextBox;
    $obTxtCodDependente->setRotulo             ( "Dependente"                                       );
    $obTxtCodDependente->setName               ( "txtCodDependente"                                 );
    $obTxtCodDependente->setId                 ( "txtCodDependente"                                 );
    $obTxtCodDependente->setTitle              ( "Selecione o dependente que tem direito a pensão." );
    $obTxtCodDependente->setSize               ( 10                                                 );
    $obTxtCodDependente->setInteiro            ( true                                               );
    $obTxtCodDependente->setNull               ( false                                              );

    $obSelDependentes = new Select;
    $obSelDependentes->setRotulo              ( 'Dependentes'      );
    $obSelDependentes->setName                ( 'inCodDependente'  );
    $obSelDependentes->setId                  ( 'inCodDependente'  );
    $obSelDependentes->setValue               ( $inCodDepentente   );
    $obSelDependentes->setStyle               ( "width: 200px"     );
    $obSelDependentes->setCampoID             ( 'cod_dependente'   );
    $obSelDependentes->setCampoDesc           ( 'nom_cgm'          );
    $obSelDependentes->addOption              ( '', 'Selecione'    );
    $obSelDependentes->setNull                ( false              );
    $obSelDependentes->preencheCombo          ( $rsDependentes     );

    $obRdoPensaoJudicial = new Radio;
    $obRdoPensaoJudicial->setName               ( 'rdoTipoPensao'                         );
    $obRdoPensaoJudicial->setId                 ( 'stRdoTipoPensao'                       );    
    $obRdoPensaoJudicial->setTitle              ( 'Selecione o tipo de pensão a ser paga' );
    $obRdoPensaoJudicial->setRotulo             ( '*Tipo da Pensão'                       );
    $obRdoPensaoJudicial->setLabel              ( 'Judicial'                              );
    $obRdoPensaoJudicial->setValue              ( 'J'                                     );
    $obRdoPensaoJudicial->setChecked            ( true                                    );

    $obRdoPensaoAmigavel = new Radio;
    $obRdoPensaoAmigavel->setName               ( 'rdoTipoPensao'                         );
    $obRdoPensaoAmigavel->setId                 ( 'stRdoTipoPensao'                       );
    $obRdoPensaoAmigavel->setTitle              ( 'Selecione o tipo de pensão a ser paga' );
    $obRdoPensaoAmigavel->setRotulo             ( '*Tipo da Pensão'                       );
    $obRdoPensaoAmigavel->setLabel              ( 'Amigável'                              );
    $obRdoPensaoAmigavel->setValue              ( 'A'                                     );
    $obRdoPensaoAmigavel->setChecked            ( false                                   );

    $obTxtOBS = new TextArea;
    $obTxtOBS->setName          ( 'txtOBS'         );
    $obTxtOBS->setId            ( 'txtOBS'         );
    $obTxtOBS->setRotulo        ( 'Observação'     );
    $obTxtOBS->setTitle         ( 'Utiliza este campo para preencher informações sobre a pensão concedida.');
    $obTxtOBS->setMaxCaracteres ( 200              );

    $txtPercentual = new Moeda;
    $txtPercentual->setRotulo             ( 'Percentual'   );
    $txtPercentual->setName               ( 'stPercentual' );
    $txtPercentual->setId                 ( 'stPercentual' );
    $txtPercentual->setTitle              ( 'Informe o percentual de desconto que será utilizado pela função de cálculo.');
    $txtPercentual->setFloat              ( true           );
    $txtPercentual->setMaxLength          ( 7              );
    $txtPercentual->obEvento->setOnChange ( "validaPercentual(this, 'Percentual de desconto'); ");

    $dtDataInclusao = new Data;
    $dtDataInclusao->setName   ( 'dtDataInclusao'                                     );
    $dtDataInclusao->setId     ( 'dtDataInclusao'                                     );
    $dtDataInclusao->setValue  ( $request->get('dtDataInclusao')                      );
    $dtDataInclusao->setTitle  ( 'Informe a data a partir da qual será paga a pensão' );
    $dtDataInclusao->setNull   ( false                                                );
    $dtDataInclusao->setRotulo ( 'Data Inclusão'                                      );
    $dtDataInclusao->setSize   ( 15                                                   );

    $dtDataLimite = new Data;
    $dtDataLimite->setName   ( 'dtDataLimite'                         );
    $dtDataLimite->setId     ( 'dtDataLimite'                         );
    $dtDataLimite->setValue  ( $request->get('dtDataLimite')          );
    $dtDataLimite->setTitle  ( 'Informe a data limite para pagamento' );
    $dtDataLimite->setNull   ( true                                   );
    $dtDataLimite->setRotulo ( 'Data Limite'                          );
    $dtDataLimite->setSize   ( 15                                     );
    $dtDataLimite->obEvento->setOnChange ( 'validaDataLimite()' );

    $obRdoValor = new Radio;
    $obRdoValor->setName               ( 'rdoTipoDesconto'                                           );
    $obRdoValor->setId                 ( 'rdoTipoDesconto'                                           );
    $obRdoValor->setTitle              ( 'Informe se o desconto será fixado por valor ou por função' );
    $obRdoValor->setRotulo             ( 'Desconto Fixado'                                           );
    $obRdoValor->setLabel              ( 'Valor'                                                     );
    $obRdoValor->setValue              ( 'V'                                                         );
    $obRdoValor->setChecked            ( true                                                        );
    $obRdoValor->obEvento->setOnChange ( "buscaValor('montaSpanValor');"                             );

    $obRdoFuncao = new Radio;
    $obRdoFuncao->setName               ( 'rdoTipoDesconto'                                           );
    $obRdoFuncao->setId                 ( 'rdoTipoDesconto'                                           );
    $obRdoFuncao->setTitle              ( 'Informe se o desconto será fixado por valor ou por função' );
    $obRdoFuncao->setRotulo             ( 'Desconto Fixado'                                           );
    $obRdoFuncao->setLabel              ( 'Função'                                                    );
    $obRdoFuncao->setValue              ( 'F'                                                         );
    $obRdoFuncao->setChecked            ( false                                                       );
    $obRdoFuncao->obEvento->setOnChange ( "buscaValor('montaSpanFuncao');"                            );

    $obspnValorFuncao = new Span;
    $obspnValorFuncao->setID ( 'spnValorFuncao' );

    // banco
    $obRPessoalPensao->obRMONBanco->listarBanco($rsBanco);

    $obTxtCodBanco = new TextBox;
    $obTxtCodBanco->setRotulo             ( "Banco"                                  );
    $obTxtCodBanco->setName               ( "inCodBanco"                             );
    $obTxtCodBanco->setId                 ( "inCodBanco"                             );
    $obTxtCodBanco->setValue              ( $inCodBanco                              );
    $obTxtCodBanco->setTitle              ( "Selecione o banco."                      );
    $obTxtCodBanco->setSize               ( 10                                       );
    $obTxtCodBanco->setMaxLength          ( 10                                        );
    $obTxtCodBanco->setNull               ( false                                    );
    $obTxtCodBanco->obEvento->setOnChange ( "buscaValor('preencheAgenciaBancaria');" );

    $obCmbCodBanco = new Select;
    $obCmbCodBanco->setName               ( "stBanco"                                );
    $obCmbCodBanco->setId                 ( "stBanco"                                );
    $obCmbCodBanco->setValue              ( $inCodBanco                              );
    $obCmbCodBanco->setRotulo             ( "Banco"                                  );
    $obCmbCodBanco->setTitle              ( 'Selecione o banco'                      );
    $obCmbCodBanco->setNull               ( false                                    );
    $obCmbCodBanco->setCampoId            ( "num_banco"                              );
    $obCmbCodBanco->setCampoDesc          ( "nom_banco"                              );
    $obCmbCodBanco->addOption             ( "", "Selecione"                          );
    $obCmbCodBanco->preencheCombo         ( $rsBanco                                 );
    $obCmbCodBanco->setStyle              ( "width: 250px"                           );
    $obCmbCodBanco->obEvento->setOnChange ( "buscaValor('preencheAgenciaBancaria');" );

    //Selecão do agencia bancaria
    $obTxtCodAgenciaBanco = new TextBox;
    $obTxtCodAgenciaBanco->setRotulo    ( 'Agência'             );
    $obTxtCodAgenciaBanco->setName      ( 'inCodAgencia'        );
    $obTxtCodAgenciaBanco->setId        ( 'inCodAgencia'        );
    $obTxtCodAgenciaBanco->setValue     ( $inCodAgencia         );
    $obTxtCodAgenciaBanco->setTitle     ( 'Selecione a agência' );
    $obTxtCodAgenciaBanco->setSize      ( 10                    );
    $obTxtCodAgenciaBanco->setMaxLength ( 10                    );
    $obTxtCodAgenciaBanco->setNull      ( false                 );

    $obCmbAgenciaBanco = new Select;
    $obCmbAgenciaBanco->setName         ( 'stAgenciaBanco'      );
    $obCmbAgenciaBanco->setId           ( 'stAgenciaBanco'      );
    $obCmbAgenciaBanco->setValue        ( $inCodAgencia         );
    $obCmbAgenciaBanco->setRotulo       ( 'Agência'             );
    $obCmbAgenciaBanco->setTitle        ( 'Selecione a agência' );
    $obCmbAgenciaBanco->setNull         ( false                  );
    $obCmbAgenciaBanco->setCampoId      ( '[cod_agencia_banco]' );
    $obCmbAgenciaBanco->setCampoDesc    ( 'descricao'           );
    $obCmbAgenciaBanco->addOption       ( '', 'Selecione'       );
    $obCmbAgenciaBanco->setStyle        ( 'width: 250px'        );

    $obTxtContaCorrente = new TextBox;
    $obTxtContaCorrente->setName      ( 'txtContaCorrente' );
    $obTxtContaCorrente->setId        ( 'txtContaCorrente' );
    $obTxtContaCorrente->setTitle     ( 'Informe a conta corrente' );
    $obTxtContaCorrente->setRotulo    ( 'Conta Corrente'   );
    $obTxtContaCorrente->setNull      ( false              );
    $obTxtContaCorrente->setMaxLength ( 10                 );

    $obRdoRespDependente = new Radio;
    $obRdoRespDependente->setName               ( 'rdoResponsavel'                                               );
    $obRdoRespDependente->setId                 ( 'rdoResponsavel'                                               );
    $obRdoRespDependente->setTitle              ( 'Informe se o responsável pela conta corrente será o próprio
                                                                     dependente ou será um responsável legal.'   );
    $obRdoRespDependente->setRotulo             ( 'Responsável pela Conta'                                       );
    $obRdoRespDependente->setLabel              ( 'Depentente'                                                   );
    $obRdoRespDependente->setValue              ( 'D'                                                            );
    $obRdoRespDependente->setChecked            ( true                                                           );
    $obRdoRespDependente->obEvento->setOnChange ( "document.getElementById('spnResponsavel').innerHTML = ''; \n" ); // esta evento faz desaparecer o spanResponsavel

    $obRdoRespLegal = new Radio;
    $obRdoRespLegal->setName               ( 'rdoResponsavel'                      );
    $obRdoRespLegal->setId                 ( 'rdoResponsavel'                      );
    $obRdoRespLegal->setLabel              ( 'Responsável Legal'                   );
    $obRdoRespLegal->setValue              ( 'R'                                   );
    $obRdoRespLegal->setChecked            ( false                                 );
    $obRdoRespLegal->obEvento->setOnChange ( "buscaValor('montaSpanResponsavel');" );

    $obSpnResponsavel = new Span;
    $obSpnResponsavel->setId( 'spnResponsavel' );

    $obRPessoalPensao->listarIncidencias ($rsIncidencias );

    $aComponentes = array();
    $titulo = 'Incidências';

    while ( !$rsIncidencias->eof() ) {
        $aLinha = array ();
        $aLinha['check'] = criaCheckBox ( 'chIncidencia_'.$rsIncidencias->getCampo('cod_incidencia'), $titulo , 'Informe em quais parâmetros para o cálculo o desconto incidirá.') ;
        $aLinha['label'] = criaLabel    ( 'lbIncidenci_'.$rsIncidencias->getCampo('cod_incidencia'), $rsIncidencias->getCampo('descricao')  );
        $aComponentes[] = $aLinha;
        $rsIncidencias->proximo();
        $titulo = '';
    }

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName ( "btnIncluir" );
    $obBtnIncluir->setId   ( "btnIncluir" );
    $obBtnIncluir->setValue( "Incluir"    );
    $obBtnIncluir->setTipo ( "button"     );
    $obBtnIncluir->obEvento->setOnClick ( "incluir ();" );

    $obBtnAlterar = new Button;
    $obBtnAlterar->setName              ( 'btnAlterar'             );
    $obBtnAlterar->setId                ( 'btnAlterar'             );
    $obBtnAlterar->setValue             ( 'Alterar'                );
    $obBtnAlterar->setTipo              ( 'button'                 );
    $obBtnAlterar->setDisabled          ( true                     );
    $obBtnAlterar->obEvento->setOnClick ( "buscaValor('alterar');" );

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName  ( 'btnLimpar' );
    $obBtnLimpar->setId    ( 'btnLimpar' );
    $obBtnLimpar->setValue ( 'Limpar'    );
    $obBtnLimpar->setTipo  ( 'button'    );
    $obBtnLimpar->obEvento->setOnClick ( "buscaValor('limpar'); " );

    $spnLista = new Span;
    $spnLista->setId ('spnLista');

    $obFormulario->addHidden             ( $obHdnCtrl                                         );
    $obFormulario->addHidden             ( $obHdnRegistroAtual                                );
    $obFormulario->addHidden             ( $obHdnEval, true                                   );
    $obFormulario->addTitulo             ( 'Matrícula'                                         );
    $obFormulario->addComponente         ( $obLblCGM                                          );
    $obFormulario->addComponente         ( $obLblContrato                                     );
    $obFormulario->addTitulo             ( 'Dependentes'                                      );
    $obFormulario->addComponenteComposto ( $obTxtCodDependente,  $obSelDependentes            );
    $obFormulario->AgrupaComponentes     ( array ($obRdoPensaoJudicial, $obRdoPensaoAmigavel) );
    $obFormulario->addComponente         ( $obTxtOBS                                          );
    $obFormulario->addComponente         ( $txtPercentual                                     );
    $obFormulario->addComponente         ( $dtDataInclusao                                    );
    $obFormulario->addComponente         ( $dtDataLimite                                      );
    $obFormulario->AgrupaComponentes     ( array ( $obRdoValor, $obRdoFuncao )                );
    $obFormulario->addSpan               ( $obspnValorFuncao                                  );
    $obFormulario->addTitulo             ( 'Informações Bancárias'                            );
    $obFormulario->addComponenteComposto ( $obTxtCodBanco, $obCmbCodBanco                     );
    $obFormulario->addComponenteComposto ( $obTxtCodAgenciaBanco, $obCmbAgenciaBanco          );
    $obFormulario->addComponente         ( $obTxtContaCorrente                                );
    $obFormulario->AgrupaComponentes     ( array ($obRdoRespDependente,  $obRdoRespLegal)     );
    $obFormulario->addSpan               ( $obSpnResponsavel                                  );
    $obFormulario->addTitulo             ( 'Incidências'                                      );

    /* aqui serão adcionados os check box */

    $arLinha  = array();
    $nColunas = 1; // numero de colunas do conjunto de checkBox
    $linha    = 0;

    foreach ($aComponentes as  $aComp) {
        $arLinha [] = $aComp['check'];
        $arLinha [] = $aComp['label'];
        $linha++;

        // verificando se a linha já tem todas as colunas preenchidas
        if ($linha  == $nColunas) {
             // adicionando uma nova linha ao formulario
             $obFormulario->AgrupaComponentes ( $arLinha );
             $arLinha = array();
             $linha = 0;
        }
    }
    if ( count($arLinha) > 0) {
        $obFormulario->AgrupaComponentes ( $arLinha );
    }

    $obFormulario->defineBarra ( array ($obBtnIncluir,$obBtnAlterar,$obBtnLimpar),'','','' );
    $obFormulario->addSpan     ( $spnLista );

    $obBtnOk = new Button;
    $obBtnOk->setName ( "btnOk" );
    $obBtnOk->setId   ( "btnOk" );
    $obBtnOk->setValue( 'Ok');
    $obBtnOk->setTipo ( "button"     );
    $obBtnOk->obEvento->setOnClick ( " ok() " );
    $obBtnOk->setStyle ( "width: 75px" );

    $obBtnLimparCampos = new Button;

    $obBtnLimparCampos->setName                    ( 'btnLimparCampos'             );
    $obBtnLimparCampos->setId                      ( 'btnLimparCampos'             );
    $obBtnLimparCampos->setValue                   ( 'Limpar'                      );
    $obBtnLimparCampos->setTipo                    ( 'button'                      );
    $obBtnLimparCampos->obEvento->setOnClick       ( "buscaValor('limparCampos');" );
    $obBtnLimparCampos->setDisabled                ( false                         );
    $obBtnLimparCampos->setStyle ( "width: 75px" );

    $obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimparCampos ), '', '', '' );
    // mostrando a listagem
    $obFormulario->show();
    montaSpanValor( true );
    montaSpanLista( true ,$request);
}

?>
