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
* Página de Listagem da Objeto
* Data de Criação   : 04/07/2007

* @author Analista: Diego Victoria
* @author Desenvolvedor: Leandro André Zis

* @ignore

* $Id: LSProcurarContrato.php 64256 2015-12-22 16:06:28Z michel $

* Casos de uso :uc-03.04.07, uc-03.04.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContrato.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript  = "function insereObjeto(inNumContrato, stDescricao, stExercicio){
                        window.opener.parent.frames['telaPrincipal'].document.getElementById('".$request->get('campoNom')."').innerHTML = stDescricao;
                        if(window.opener.parent.frames['telaPrincipal'].document.getElementById('stExercicioContrato')){
                           window.opener.parent.frames['telaPrincipal'].document.getElementById('stExercicioContrato').value = stExercicio;
                        }
                        window.opener.parent.frames['telaPrincipal'].document.".$request->get('nomForm').".".$request->get('campoNum').".value = inNumContrato;
                        window.opener.parent.frames['telaPrincipal'].document.".$request->get('nomForm').".".$request->get('campoNum').".focus();
                        window.close();
                     } \n";

$stCaminho = CAM_GP_COM_INSTANCIAS."objeto/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'excluir');

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$stLink = "&stAcao=".$stAcao;

$filtro = Sessao::read('filtro');
if ( $request->get('stHdnDescricao') || $request->get('inCodEntidade') || ( $request->get('stDataInicial') && $request->get('stDataFinal') ) || $filtro=='' ){
    foreach ( $request->getAll() as $key => $value ){
        $filtro[$key] = $value;
    }
}else{
    if( $filtro ){
        foreach ( $filtro as $key => $value ){
            $request->set($key, $value);
        }
    }
    Sessao::write('paginando', true);
}
Sessao::write('filtro', $filtro);

$obTLicitacaoContrato = new TLicitacaoContrato;

if ( $request->get('stDataInicial') )
    $stFiltro .= " and contrato.dt_assinatura >= to_date( '".$request->get('stDataInicial')."', 'dd/mm/yyyy')  ";

if ( $request->get('stDataFinal') )
    $stFiltro .= " and contrato.dt_assinatura <= to_date( '".$request->get('stDataFinal')."' ,'dd/mm/yyyy' )";

$obTLicitacaoContrato->setDado ( 'cod_entidade', $request->get('inCodEntidade') );

if( $request->get('boFornecedor') ){
    $inCodEntidade = $request->get('inCodEntidade', '');
    $stCodEntidade = ($inCodEntidade!='') ? '= '.$inCodEntidade : 'IS NULL';
    $stFiltro .= " and contrato.cod_entidade ".$stCodEntidade;

    $inCodFornecedor = $request->get('inCodFornecedor', '');
    $stCodFornecedor = ($inCodFornecedor!='') ? '= '.$inCodFornecedor : 'IS NULL';
    $stFiltro .= " and contrato.cgm_contratado ".$stCodFornecedor;
    
    if($request->get('stExercicio'))
        $stFiltro .= " and contrato.exercicio = '".$request->get('stExercicio')."'";

    $obTLicitacaoContrato->recuperaContratoEmpenho ( $rsLista, $stFiltro );
}else
    $obTLicitacaoContrato->recuperaRelacionamento ( $rsLista, $stFiltro );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Contratos");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Assinatura" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Objeto" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_contrato" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "dt_assinatura" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereObjeto();" );
$obLista->ultimaAcao->addCampo("1","num_contrato");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->ultimaAcao->addCampo("3","exercicio");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
