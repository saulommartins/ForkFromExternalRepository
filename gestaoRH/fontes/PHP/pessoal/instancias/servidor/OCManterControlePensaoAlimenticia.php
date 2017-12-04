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
* Página processamento oculto de Controle de Pensão Alimenticia
* Data de: Criação   : 03/04/2006
# 20060419

* @author Analista: Vandré Miguel Ramos.
* @author Desenvolvedor: Bruce Cruz de Sena

* @ignore

* Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                  );

include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                         );
include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalDependente.class.php'                                      );
include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalPensao.class.php'                                          );

function montaSpanContrato($boExecuta=false)
{
    $obIFiltroContrato = new IFiltroContrato('todos',false);
    $obIFiltroContrato->setInformacoesFuncao  ( false );
    $obIFiltroContrato->obIContratoDigitoVerificador->setNull( false );

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario ( $obFormulario );

    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    $stJs .= "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';     \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanCGMContrato($boExecuta=false)
{
    $obIFiltroCGMContrato = new IFiltroCGMContrato('todos',false);
    $obIFiltroCGMContrato->setInformacoesFuncao  ( false );
    $obIFiltroCGMContrato->obCmbContrato->setNull( false );

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.stEval.value                       = '".$stEval."';                     \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanValor($boExecuta = false)
{
    global $_POST;
    $stJs = '';

    $obFormulario = new Formulario;

    $obValor = new Moeda;
    $obValor->setName   ( 'flValor'                                     );
    $obValor->setId     ( 'flValor'                                     );
    $obValor->setRotulo ( 'Valor'                                       );
    $obValor->setTitle  ( 'Informe o valor a ser utilizado no desconto' );
    $obValor->setNull   ( false                                         );

    $obFormulario->addComponente ( $obValor );
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $stJs  .= "f.stEval.value = '".trim($stEval)."'; \n";

    $stJs .= "d.getElementById('spnValorFuncao').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "d.frm.chIncidencia_6.checked  = false; \n ";
    $stJs .= "d.frm.chIncidencia_6.disabled = true; \n  ";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }

}//function montaSpanValor($boExecuta = false) {

function montaSpanFuncao($boExecuta = false)
{
    global $_POST;
    $obFormulario =  new Formulario;

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPensaoFuncaoPadrao.class.php");
    $obTFolhaPagamentoPensaoFuncaoPadrao = new TFolhaPagamentoPensaoFuncaoPadrao;
    $obTFolhaPagamentoPensaoFuncaoPadrao->recuperaUltimaPensaoFuncaoPadrao( $rsFuncao );
    if ( $rsFuncao->getNumLinhas() == 1 ) {
        $stCodFuncao = $rsFuncao->getCampo('cod_modulo')     .'.'.
                       $rsFuncao->getCampo('cod_biblioteca') .'.'.
                       $rsFuncao->getCampo('cod_funcao');

       $obRFuncao = new RFuncao;
       $obRFuncao->setCodFuncao                           ( $rsFuncao->getCampo('cod_funcao') );
       $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $rsFuncao->getCampo('cod_biblioteca') );
       $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $rsFuncao->getCampo('cod_modulo') );
       $obRFuncao->consultar();
       $stNomeFuncao = $obRFuncao->getNomeFuncao();
    }

    $obBscFuncao = new BuscaInner;
    $obBscFuncao->setRotulo ( "Função"                                         );
    $obBscFuncao->setTitle  ( "Informe a função a ser utilizada no cálculo."    );
    $obBscFuncao->setId     ( "stFuncao"                                        );
    $obBscFuncao->setNull   ( false                                             );
    $obBscFuncao->obCampoCod->setName   ( "inCodFuncao" );
    $obBscFuncao->obCampoCod->setTitle  ( 'Informe a função a ser utilizada no cálculo.' );
    $obBscFuncao->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
    $obBscFuncao->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaFuncao');");
    $obBscFuncao->obCampoCod->setMascara("99.99.999");
    $obBscFuncao->obCampoCod->setNull   ( false     );
    $obBscFuncao->obCampoCod->setValue($stCodFuncao);
    $obBscFuncao->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFuncao','todos','".Sessao::getId()."','800','550');" );

    $obFormulario->addComponente ( $obBscFuncao );

    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $stJs  .= "f.stEval.value = '".trim($stEval)."'; \n";

    $stJs .= "d.getElementById('spnValorFuncao').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "d.getElementById('stFuncao').innerHTML = '".$stNomeFuncao."';    \n";
    $stJs .= "d.frm.chIncidencia_6.disabled = false; \n ";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}//function montaSpanFuncao($boExecuta = false) {

function buscaFuncao($boExecuta = false)
{
    if ($_POST['inCodFuncao']) {
        $arCodFuncao = explode('.',$_POST['inCodFuncao']);
        $obRFuncao = new RFuncao;
        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
        $obRFuncao->consultar();
        $stNomeFuncao = $obRFuncao->getNomeFuncao();
        if ( !empty($stNomeFuncao) ) {
            $stJs .= "d.getElementById('stFuncao').innerHTML = '".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value = '';\n";
            $stJs .= "f.inCodFuncao.focus();\n";
            $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_POST['inCodFuncao'].")','form','erro','".Sessao::getId()."');";
        }
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}//function buscaFuncao(

function montaSpanResponsavel ( $boExecuta = false)
{
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo                         ( 'CGM do Responsável pela Conta'                  );
    $obBscCGM->setTitle                          ( 'Informe o CGM do responsável pelo recebimento.' );
    $obBscCGM->setNull                           ( false                                            );
    $obBscCGM->setId                             ( 'inNomCGM'                                       );
    $obBscCGM->obCampoCod->setName               ( 'inNumCGM'                                       );
    $obBscCGM->obCampoCod->setId                 ( 'inNumCGM'                                       );
    $obBscCGM->obCampoCod->setValue              ( $inNumCGM                                        );
    $obBscCGM->obCampoCod->obEvento->setOnChange ( "buscaValor('buscaCGM');"                        );
    $obBscCGM->obCampoCod->obEvento->setOnBlur   ( "buscaValor('buscaCGM');"                        );
    $obBscCGM->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGM','inNomCGM','fisica','".Sessao::getId()."','800','550')" );

    $obLblEndereco = new Label;
    $obLblEndereco->setName   ( 'lblEndereco' );
    $obLblEndereco->setId     ( 'lblEndereco' );
    $obLblEndereco->setRotulo ( 'Endereço'    );

    $obLblCEP = new Label;
    $obLblCEP->setName   ( 'lblCEP' );
    $obLblCEP->setId     ( 'lblCEP' );
    $obLblCEP->setRotulo ( 'CEP'    );

    $obLblTelefone = new Label;
    $obLblTelefone->setName   ( 'lblTelefone' );
    $obLblTelefone->setId     ( 'lblTelefone' );
    $obLblTelefone->setRotulo ( 'Telefone'    );

    $obFormulario = new Formulario;
    $obFormulario->addComponente ( $obBscCGM      );
    $obFormulario->addComponente ( $obLblEndereco );
    $obFormulario->addComponente ( $obLblCEP      );
    $obFormulario->addComponente ( $obLblTelefone );

    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnResponsavel').innerHTML = '".$obFormulario->getHTML()."';    \n";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }

}//function montaSpanResponsavel($boExecuta = false) {

function montaSpanLista($boExecuta = false,Request $request)
{
    $rsRecordSet = new Recordset; //será preenchido com as dados dos dependentes que estarão em uma array

    if ( count ( Sessao::read('aPensoes') ) > 0 ) {
        $rsRecordSet->preenche( Sessao::read('aPensoes') );
    }

    // Montagem Lista
    $obLstPensoes = new Lista;
    $obLstPensoes->setTitulo          ( 'Dependentes com Direito a Pensão' );
    $obLstPensoes->setMostraPaginacao ( false                              );
    $obLstPensoes->setRecordset       ( $rsRecordSet                       );
    // Cabeçalho da lista
    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo ( "&nbsp;"     );
    $obLstPensoes->ultimoCabecalho->setWidth    ( 3            );
    $obLstPensoes->commitCabecalho();

    // Cabeçalho da lista
    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo    ( 'Dependente' );
    $obLstPensoes->ultimoCabecalho->setWidth       ( 25           );
    $obLstPensoes->commitCabecalho();
    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo    ( 'Tipo de Pensão' );
    $obLstPensoes->ultimoCabecalho->setWidth       ( 10               );
    $obLstPensoes->commitCabecalho();
    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo    ( 'Data de Inclusão' );
    $obLstPensoes->ultimoCabecalho->setWidth       ( 12                 );
    $obLstPensoes->commitCabecalho();
    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo    ( 'Função' );
    $obLstPensoes->ultimoCabecalho->setWidth       ( 20       );
    $obLstPensoes->commitCabecalho();

    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo    ( 'Valor' );
    $obLstPensoes->ultimoCabecalho->setWidth       ( 10      );
    $obLstPensoes->commitCabecalho();
    $obLstPensoes->addCabecalho();
    $obLstPensoes->ultimoCabecalho->addConteudo    ( 'Ação' );
    $obLstPensoes->ultimoCabecalho->setWidth       ( 5      );
    $obLstPensoes->commitCabecalho();

    //dados da Lista
    $obLstPensoes->addDado();
    $obLstPensoes->ultimoDado->setCampo( '[numcgm] - [dependente]' );
    $obLstPensoes->commitDado();

    $obLstPensoes->addDado();
    $obLstPensoes->ultimoDado->setCampo( 'tipo_pensao' );
    $obLstPensoes->commitDado();

    $obLstPensoes->addDado();
    $obLstPensoes->ultimoDado->setCampo( 'dataInclusao' );
    $obLstPensoes->commitDado();

    $obLstPensoes->addDado();
    $obLstPensoes->ultimoDado->setCampo( 'evento' );
    $obLstPensoes->commitDado();

    $obLstPensoes->addDado();
    $obLstPensoes->ultimoDado->setCampo( 'valor' );
    $obLstPensoes->ultimoDado->setAlinhamento( 'DIREITA');
    $obLstPensoes->commitDado();

    $obLstPensoes->addAcao();
    $obLstPensoes->ultimaAcao->setAcao( "ALTERAR" );
    $obLstPensoes->ultimaAcao->setFuncao( true );
    $obLstPensoes->ultimaAcao->setLink( "JavaScript:alteraDado('alterarPensao');" );
    $obLstPensoes->ultimaAcao->addCampo("1","inId");
    $obLstPensoes->commitAcao();

    // Adicionando ação excluir a lista
    $obLstPensoes->addAcao();
    $obLstPensoes->ultimaAcao->setAcao( "EXCLUIR" );
    $obLstPensoes->ultimaAcao->setFuncao( true );
    $obLstPensoes->ultimaAcao->setLink( "JavaScript:alteraDado('excluirPensao');" );
    $obLstPensoes->ultimaAcao->addCampo("1","inId");
    $obLstPensoes->commitAcao();

    $obLstPensoes ->montaHTML();
    $stHtml = $obLstPensoes->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnLista').innerHTML = '".$stHtml."';";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }

}//function montaSpanLista($boExecuta = false) {

/*
função     : busca Pensões
objetivo   : recebe o numero de contrato de um servidor e preenche a array
             "sessao->transf['aPensoes']" com os dados das pensões do servidor
Parametros : inContrato
Retorno    : obErro
*/

function buscaPensoes($inCodContrato)
{
    Sessao::write('aFuncoes',array ());

}//function buscaPensoes($inCodContrato) {

function preencheAgenciaBancaria($inCodBanco ='',  $boExecuta = false)
{
    $obRPessoalPensao = new RPessoalPensao ( );

    $stJs .= "limpaSelect(f.stAgenciaBanco,0); \n";
    $stJs .= "f.inCodAgencia.value = ''; \n";
    $stJs .= "f.stAgenciaBanco[0] = new Option('Selecione','','selected');\n";

    if ($inCodBanco) {
        $obRPessoalPensao->obRMONAgencia->obRMONBanco->setNumBanco ($inCodBanco);
        $obRPessoalPensao->obRMONAgencia->listarAgencia($rsAgenciaBancaria,$boTransacao);
        $inContador = 1;
        while ( !$rsAgenciaBancaria->eof() ) {
            $inCodAgenciaBancaria  = $rsAgenciaBancaria->getCampo( "num_agencia" );
            $stAgenciaBancaria     = $rsAgenciaBancaria->getCampo( "nom_agencia" );

            $stJs .= "f.stAgenciaBanco.options[$inContador] = new Option('".$stAgenciaBancaria."','".$inCodAgenciaBancaria."'); \n";
            $inContador++;
            $rsAgenciaBancaria->proximo();
        }
    } elseif ($_POST["inCodBanco"] == '0') {
        $stJs .= "f.inCodAgencia.value = '0'; \n";
        $stJs .= "f.stAgenciaBanco.options[0] = new Option('Não informado','0'); \n";
    }

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}//function preencheAgenciaBancaria($boExecuta = false) {

function buscaCGM($boExecuta = false, $inCodigo = '')
{
    global $_POST;

    $stJs       = '';
    $stEndereco = '';

    $rsCGM1 = new Recordset;
    $obRCGM = new RCGMPessoaFisica;

    if (!$inCodigo) { $inCodigo = $_POST['inNumCGM']; }
    if ($inCodigo != "") {
        $obRCGM->setNumCGM ( $inCodigo );
        $obRCGM->ConsultarCGM ( $rsCGM1, $boTransacao="" );
        if ( $rsCGM1->getNumLinhas() <= 0 ) {
           $stJs .= " alertaAviso('@Código não encontrado. (".$inCodigo.")','form','erro','".Sessao::getId()."');";
           $stJs .= " d.getElementById('lblEndereco').innerHTML = '&nbsp'; \n";
           $stJs .= " d.getElementById('lblTelefone').innerHTML = '&nbsp'; \n";
           $stJs .= " d.getElementById('lblCEP'     ).innerHTML = '&nbsp'; \n";
           $stJs .= " d.getElementById('inNomCGM'   ).innerHTML = '&nbsp'; \n";
           $stJs .= " f.inNumCGM.value                          = ''; \n";
        } else {
           $stEndereco .= $rsCGM1->getCampo( 'logradouro' ) .', ' .$rsCGM1->getCampo('numero');
           if ( trim ( $rsCGM1->getCampo('complemento') ) != '' ) {
               $stEndereco .= ' - ' . $rsCGM1->getCampo('complemento');
           }
           $stJs .= " d.getElementById('lblEndereco').innerHTML = '" . $stEndereco                            . "'; \n";
           $stJs .= " d.getElementById('lblTelefone').innerHTML = '" . $rsCGM1->getCampo ('fone_residencial') . "'; \n";
           $stJs .= " d.getElementById('lblCEP'     ).innerHTML = '" . $rsCGM1->getCampo ('cep')              . "'; \n";
           $stJs .= " d.getElementById('inNomCGM'   ).innerHTML = '" . $rsCGM1->getCampo ('nom_cgm')          . "'; \n";
        }
    } else {
        $stJs .= " d.getElementById('lblEndereco').innerHTML = '&nbsp'; \n";
        $stJs .= " d.getElementById('lblTelefone').innerHTML = '&nbsp'; \n";
        $stJs .= " d.getElementById('lblCEP'     ).innerHTML = '&nbsp'; \n";
        $stJs .= " d.getElementById('inNomCGM'   ).innerHTML = '&nbsp'; \n";
        $stJs .= " f.inNumCGM.value                          = ''; \n";
    }

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}//function buscaCGM($boExecuta = false) {

/*
Inclui os dados da tela atual na array que é mostrada na listagem
*/
function incluir($boExecuta = false,Request $request)
{
    $stJs    = '';
    $boAchou = false;
    $stJs    = '';

    if ( count(Sessao::read('aPensoes')) > 0 ) {
        foreach ( Sessao::read('aPensoes') as $linha ) {
            if ($linha['cod_dependente']  == $request->get('inCodDependente')) {
                $boAchou = true;
            }
        }
    }

    if ($boAchou == true) {
        $stJs .= " alertaAviso('Este dependente já foi cadastrado!','form','erro','". Sessao::getId()."'); \n";
    } else {
        $obErro =  salvarRegistro($request->get('inId'),$request); // passando os dados pro array
        if ( !$obErro->ocorreu() ) {
            $stJs .= montaSpanLista( false, $request )  ;
            $stJs .= limpar ( false );            
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    }

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}//function incluir($boExecuta = false) {

/*
 preenche a tela com o registro selecionado na listagem para alteração do mesmo
*/
function alterarPensao($boExecuta = false,Request $request)
{    
    $stJs = '';
    $arTemp = array();

    /// o campo inId cotem o mesmo numero do indice da array ( por isso não precisa busca aqui);
    $arPensoes = Sessao::read("aPensoes");            
    $arTemp = $arPensoes[$request->get('inId')];

    /// este array é usado como os objetos ocultos
    $arAlteracao['inId']          = $request->get('inId');
    $arAlteracao['codDependente'] = $arTemp['cod_dependente'];
    $arAlteracao['numcgm'       ] = $arTemp['numcgm'        ];
    $arAlteracao['dependente'   ] = $arTemp['dependente'    ];
    $arAlteracao['cod_pensao'   ] = $arTemp['cod_pensao'    ];
    $arAlteracao['timeStamp'    ] = $arTemp['timeStamp'     ];
    Sessao::write('arAlteracao',$arAlteracao);
    ////// Tipo de Pensão
    if ($arTemp ['tipo_pensao'] == 'Jurídica') {        
        $stJs .= "jq_('input[value=\"J\"]').prop('checked',true); \n ";
    } else {
        $stJs .= "jq_('input[value=\"A\"]').prop('checked',true); \n ";
    }

    // combo de dependente
    $stJs .= " jq_('#txtCodDependente').val('". $arTemp['cod_dependente'] ."'); \n";
    $stJs .= " jq_('#inCodDependente').val('". $arTemp['cod_dependente'] ."'); \n";

    $arOBS = explode( "\n", $arTemp['OBS'] );
    $stOBS = '';
    if ( count ($arOBS) > 1 ) {
        foreach ($arOBS as $linha=>$texto) {
            $stOBS .= (substr($arOBS[$linha],0, strlen($arOBS[$linha])-1)) . '\n';
        }
    } else {
        $stOBS = $arTemp['OBS'];
    }    
    $stJs .= "jq_('#txtOBS').val('". $stOBS ."');" ;

    // preenchendo o combo de banco    
    $stJs .= "jq_('#inCodBanco').val('". $arTemp['codBanco']   ."'); \n ";
    $stJs .= "jq_('#stBanco').val('". $arTemp['codBanco']   ."') ; \n ";
    $stJs .= preencheAgenciaBancaria( $arTemp['codBanco'], false );
    // preenchendo a agencia
    $stJs .= "jq_('#inCodAgencia').val('" .$arTemp['codAgencia']. "'); \n";
    $stJs .= "jq_('#stAgenciaBanco').val('" .$arTemp['codAgencia']. "'); \n";

    $stJs .= "jq_('#dtDataInclusao').val('" . $arTemp[ 'dataInclusao' ] . "'); \n";
    $stJs .= "jq_('#dtDataLimite').val('" . $arTemp[ 'dataLimite'   ] . "'); \n";
    $stJs .= "jq_('#stPercentual').val('" . $arTemp[ 'Percentual'   ] . "'); \n";

    if ($arTemp['valor']) {
        $stJs .= "d.frm.rdoTipoDesconto[0].checked = true; \n ";
        $stJs .= montaSpanValor(false);
        $stJs .= "jq_('#flValor').val('". $arTemp[ 'valor' ] . "'); \n";
    } else {
        $stJs .= "d.frm.rdoTipoDesconto[1].checked = true; \n ";
        $stJs .= montaSpanFuncao( false );
        $stJs .= "jq_('#inCodFuncao').val('".$arTemp['codFuncao']."'); \n ";
        $stJs .= "jq_('#stFuncao').html('". $arTemp['Funcao']. "'); \n";
    }

    $stJs .= "jq_('#txtContaCorrente').val('". $arTemp[ 'contaCorrente' ] . "'); \n";

    if ($arTemp['inNumCGMResp']) {
        $stJs .= montaSpanResponsavel( false );
        $stJs .= "d.frm.rdoResponsavel[1].checked = true; \n";
        $stJs .= "jq_('#inNumCGM').val('" . $arTemp['inNumCGMResp'] . "'); \n";
        $stJs .= buscaCGM ( false, $arTemp['inNumCGMResp'] );  // inserindo código para buscar o nome do responsável
    } else {
        $stJs .= "d.frm.rdoResponsavel[0].checked = true; \n";
        $stJs .= "jq_('#spnResponsavel').html(''); \n ";
    }

    $stJs .= "jq_('#chIncidencia_1').prop('checked','". ($arTemp ['chIncidencia_1']?"true":"")."') ; \n";
    $stJs .= "jq_('#chIncidencia_2').prop('checked','". ($arTemp ['chIncidencia_2']?"true":"")."') ; \n";
    $stJs .= "jq_('#chIncidencia_3').prop('checked','". ($arTemp ['chIncidencia_3']?"true":"")."') ; \n";
    $stJs .= "jq_('#chIncidencia_4').prop('checked','". ($arTemp ['chIncidencia_4']?"true":"")."') ; \n";
    $stJs .= "jq_('#chIncidencia_5').prop('checked','". ($arTemp ['chIncidencia_5']?"true":"")."') ; \n";
    $stJs .= "jq_('#chIncidencia_6').prop('checked','". ($arTemp ['chIncidencia_6']?"true":"")."') ; \n";

    $stJs .= "jq_('#btnAlterar').prop('disabled', false); \n";
    $stJs .= "jq_('#btnIncluir').prop('disabled', true);  \n";
    $stJs .= "jq_('#txtCodDependente').prop('disabled',true);  \n";
    $stJs .= "jq_('#inCodDependente').prop('disabled',true);  \n";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}//function alterarPensao($boExecuta = false) {

/*
Função: limpar
objetivo: apagar os dados depois de uma inclusão ou de uma alteração
autor: Bruce Cruz de Sena
Data: 1.04.2006
*/
function limpar($boExecuta = false)
{
    $stJs .= "d.frm.rdoTipoPensao[0].checked = true; \n ";
    $stJs .= "d.frm.dtDataInclusao.value = '';       \n";
    $stJs .= "d.frm.dtDataLimite.value =   '';       \n";
    $stJs .= "d.frm.stPercentual.value =   '';       \n";

    $stJs .= "d.frm.rdoTipoDesconto[0].checked = true; \n ";

    $stJs .= "d.frm.txtContaCorrente.value = ''; \n";

    $stJs .= montaSpanValor ( false );
    $stJs .= "d.frm.flValor.value          = ''; \n";

    $stJs .= "d.frm.txtOBS.value           = ''; \n";

    $stJs .= "d.frm.rdoResponsavel[0].checked = true; \n ";
    $stJs .= montaSpanResponsavel( false );
    $stJs .= "d.getElementById('spnResponsavel').innerHTML = ''; \n";

    $stJs .= "d.frm.chIncidencia_1.checked = false; \n";
    $stJs .= "d.frm.chIncidencia_2.checked = false; \n";
    $stJs .= "d.frm.chIncidencia_3.checked = false; \n";
    $stJs .= "d.frm.chIncidencia_4.checked = false; \n";
    $stJs .= "d.frm.chIncidencia_5.checked = false; \n";
    $stJs .= "d.frm.chIncidencia_6.checked = false; \n";

    $stJs .= "f.inCodDependente.value = '' ; \n";
    $stJs .= "d.frm.txtCodDependente.value = ''; \n";
    $stJs .= "f.inCodBanco.value = '' ; \n ";
    $stJs .= "f.stBanco.value = ''; \n ";
    $stJs .= "f.inCodAgencia.value = ''; \n";
    $stJs .= "f.stAgenciaBanco.value = ''; \n ";

    $stJs .= "d.frm.btnAlterar.disabled  = true; \n";
    $stJs .= "d.frm.btnIncluir.disabled  = false;  \n";
    $stJs .= "d.frm.txtCodDependente.disabled = false; \n";
    $stJs .= "f.inCodDependente.disabled = false; \n";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }

}//function limpar($boExecuta = false) {

/*
função exclusão de pensão apenas de array
a linha ecluida da array vai para a array de excluidas
o numero da linha que deve ser excluida está em $_GET[inId]
*/
function excluirPensao($boExecuta = false,Request $request)
{
    global $_POST;
    global $_GET;
    $arTemp = array();
    $stJs   = '';
    $arReg = array();
    $arPensoes = Sessao::read("aPensoes");
    $arPensoesExcluidas = Sessao::read("arPensoesExcluidas");
    if ($arPensoes[$_GET['inId']]['cod_pensao']) {
        // se tiver cod_pensao siginifica que o registro já esta no banco de dados e tem que ser
        //excluido lá tb por iso ele vai pra array  arPensoesExcluidas que será tratada no PR
        // caso contrario ele só será excluido da array #sessao->transf['aPensoes']

        $arReg['cod_pensao'] = $arPensoes[$_GET['inId']]['cod_pensao'];
        $arReg['timeStamp' ] = $arPensoes[$_GET['inId']]['timeStamp' ];

        $arPensoesExcluidas[] = $arReg;
    }

    foreach ($arPensoes as $indice => $linha) {
        if ($indice != $_GET['inId']) {
            $arTemp[] = $linha;
        }
    }
    //este laço apenas coloca o numero do indice de cada elemento da array no campo inId
    for ( $i = 0; $i < count ( $arTemp ); $i++ ) {
        $arTemp[$i]['inId'] = $i;
    }
    Sessao::write("aPensoes",$arTemp);
    Sessao::write("arPensoesExcluidas", $arPensoesExcluidas);

    $stJs .= montaSpanLista( false, $request );

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}//function excluirPensao($boExecuta = false) {

/*
função   : salvarPensao
objetivo : salva os dados alterados no array;
data     : 18/04/2006
Autor    : Bruce Cruz de Sena;
*/

function salvarPensao($boExecuta = false,Request $request)
{
    $arAlteracao = Sessao::read("arAlteracao");    
    if ( salvarRegistro($arAlteracao['inId'],$request) ) {

        $stJs .= "d.frm.btnAlterar.disabled  = true; \n";
        $stJs .= "d.frm.btnIncluir.disabled  = false;  \n";
        $stJs .= montaSpanLista ( false , $request);
        $stJs .= limpar( false );
        sistemaLegado::exibeAviso('Pensão alterada com sucesso!','','');
    }

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }

}//function salvarPensao($boExecuta = false) {

/*
função   : salvarRegistro
objetivo : esta função será usada no método incluir e alterar para passar os dados pra array
data     : 19/04/2006
autor    : Bruce Cruz de Sena
parametro: $inId:  se for vazio os dados da tela serão incluidos no fim da array em um novo registro
                   se tiver um número de id da array este registro será alterado
retorno: obErro
*/
function salvarRegistro($inId = '',Request $request)
{
    $arRegistro = array();
    $stMsgErro = '';
    $stJs = '';
    $obErro = new erro;

    /// validações que não estão na função valida gerada pelo frameWork
    if (($request->get('rdoTipoDesconto') == 'F' ) and (!$request->get('inCodFuncao')) ) {
        $obErro->setDescricao('Informe a função a ser usada no cálculo!');
    } elseif ( (!$request->get('inNumCGM') ) and ($request->get('inNumCGM') == 'R') ) {
        $obErro->setDescricao('Informe o responsável pela conta corrente!');
    }

    if ( !$obErro->ocorreu() ) {
         $tipoPensao = ($request->get('rdoTipoPensao') == 'J') ? 'Jurídica' : 'Amigavel';
         $arRegistro[ 'tipo_pensao'     ] = $tipoPensao;
         $arRegistro[ 'OBS'             ] = $request->get('txtOBS'           ) ;
         $arRegistro[ 'Percentual'      ] = $request->get('stPercentual'     ) ;
         $arRegistro[ 'dataInclusao'    ] = $request->get('dtDataInclusao'   ) ;
         $arRegistro[ 'dataLimite'      ] = $request->get('dtDataLimite'     ) ;
         $arRegistro[ 'codBanco'        ] = $request->get('inCodBanco'       ) ;
         $arRegistro[ 'codAgencia'      ] = $request->get('inCodAgencia'     ) ;
         $arRegistro[ 'contaCorrente'   ] = $request->get('txtContaCorrente' ) ;

         if ($request->get('rdoTipoDesconto') == 'V') {
             $arRegistro['valor'] = $request->get('flValor');
         } else {
             // buscando o nome da função
             $arCodFuncao = $request->get('inCodFuncao');
             $arCodFuncao = explode('.',$arCodFuncao);
             $obRFuncao = new RFuncao;
             $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
             $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
             $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
             $obRFuncao->consultar();
             $stNomeFuncao = $obRFuncao->getNomeFuncao();

             $arRegistro[ 'evento'         ] = $request->get('inCodFuncao').' - '.$stNomeFuncao;
             $arRegistro[ 'Funcao'         ] = $stNomeFuncao;
             $arRegistro[ 'codFuncao'      ] = $request->get('inCodFuncao');
         }

         $arRegistro[ 'inNumCGMResp'   ]  = $request->get('inNumCGM');

         $arRegistro[ 'chIncidencia_1'  ] = $request->get('chIncidencia_1');
         $arRegistro[ 'chIncidencia_2'  ] = $request->get('chIncidencia_2');
         $arRegistro[ 'chIncidencia_3'  ] = $request->get('chIncidencia_3');
         $arRegistro[ 'chIncidencia_4'  ] = $request->get('chIncidencia_4');
         $arRegistro[ 'chIncidencia_5'  ] = $request->get('chIncidencia_5');
         $arRegistro[ 'chIncidencia_6'  ] = $request->get('chIncidencia_6');

         if ($inId == '') {
             // inclusão de registro
             $obRPessoalServidor = new RPessoalServidor;
             $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM ( $request->get('inCGM') );
             $obRPessoalServidor->consultarServidor             ( $rsServidor, false );
             $obRPessoalServidor->setCodServidor                ( $rsServidor->getCampo( 'cod_servidor') );
             $obRDependente = new RPessoalDependente ( $obRPessoalServidor         );
             $obRDependente->setCodDependente        ( $request->get('inCodDependente') );
             $obRDependente->consultarDependente     ( $rsDeps                     );
             $arPensoes = Sessao::read("aPensoes");
             $arRegistro[ 'inId'           ] = count ($arPensoes);
             $arRegistro[ 'numcgm'         ] = $rsDeps->getCampo('numcgm');
             $arRegistro[ 'dependente'     ] = $rsDeps->getCampo('nom_cgm');
             $arRegistro[ 'cod_dependente' ] = $request->get('inCodDependente') ;
             $arRegistro[ 'cod_pensao'     ] = null;
             $arRegistro[ 'timeStamp'      ] = null;
             $arPensoes[] = $arRegistro;
             Sessao::write("aPensoes",$arPensoes);
         } else {
             // alteração de registro
             $arAlteracao = Sessao::read("arAlteracao");
             $arPensoes   = Sessao::read("aPensoes");
             $arRegistro['inId'          ] = $inId;

             $arRegistro['cod_dependente'] = $arAlteracao['codDependente'];
             $arRegistro['numcgm'        ] = $arAlteracao['numcgm'       ];
             $arRegistro['dependente'    ] = $arAlteracao['dependente'   ];
             $arRegistro['cod_pensao'    ] = $arAlteracao['cod_pensao'   ];
             $arRegistro['timeStamp'     ] = $arAlteracao['timeStamp'    ];
             $arPensoes[$inId] = $arRegistro;             
             Sessao::write("aPensoes",$arPensoes);

         }

         // este array é resetado para não ter lixo, pois se a proxima ação do usuário for uma inclusao ela tem que estar vazia
         Sessao::write('arAlteracao',array());
    }

    return $obErro;
}//function salvarRegistro($inId = '') {

switch ( $request->get("stCtrl") ) {
    case "montaSpanContrato":
        $stJs.= montaSpanContrato( true );
    break;
    case "montaSpanCGMContrato":
        $stJs.= montaSpanCGMContrato( true );
    break;
    case 'montaSpanValor':
        montaSpanValor( true );
    break;
    case 'montaSpanFuncao':
          montaSpanFuncao( true );
    break;
    case 'montaSpanResponsavel':
          montaSpanResponsavel( true );
    break ;
    case 'montaSpanlista':
          montaSpanlista( true, $request );
    break;
    case 'buscaCGM':
          buscaCGM( true );
    break;
    case 'preencheAgenciaBancaria':
         preencheAgenciaBancaria($request->get('inCodBanco'), true );
    break;
    case 'incluir':
         incluir ( true ,$request);
    break;
    case 'alterarPensao':
         // monta a tela para a alteração
         alterarPensao(true, $request);
    break;
    case 'excluirPensao':
        excluirPensao( true, $request );
    break;
    case 'limpar':
        limpar(true);
    break;
    case 'preencherEvento':
          preencherEvento( true );
    break;
    case 'buscaFuncao':
          buscaFuncao ( true );
    break;
    case 'alterar':
        // salva a alteração
        salvarPensao( true ,$request);
    break;
    case 'limparCampos':
        if ($request->get('rdoOpcao') == 'G') {
            montaSpanCGMContrato(true);
        } else {
            montaSpanContrato(true);
        }
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto( $stJs );
}
?>
