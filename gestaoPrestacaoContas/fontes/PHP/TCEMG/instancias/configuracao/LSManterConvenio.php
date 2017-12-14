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
	* Lista de Convenio TCEMG
	* Data de Criação   : 12/03/2014

	* @author Analista: Sergio Luiz dos Santos
	* @author Desenvolvedor: Michel Teixeira
	* @ignore

	$Id: LSManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

	*Casos de uso:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

$stCaminho = CAM_GPC_TCEMG_INSTANCIAS."configuracao/";

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$stExercicio	= $_REQUEST['stExercicio'	];
$inCodEntidade	= $_REQUEST['inCodEntidade'	];
$inNumConvenio	= $_REQUEST['inNumConvenio'	];
$dtInicial		= $_REQUEST['dtInicial'		];
$dtFinal		= $_REQUEST['dtFinal'		];

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
    case 'anular':
        $pgProx = $pgForm; break;
    case 'consultar':
        $pgProx = $pgForm; break;
    case 'rescindir':
        $pgProx = $pgForm; break;
}

function montaListaAlteracao($rsLista , $stJs = null , $stAcao = 'alterar')
{
    global $pgProc;
    global $pgForm;
    global $stCaminho;
    
    $stLink = "";
  
    $rsLista->addFormatacao( 'vl_convenio' 		, 'NUMERIC_BR' );
    $rsLista->addFormatacao( 'vl_contra_partida', 'NUMERIC_BR' );
    
    $rsLista->setPrimeiroElemento();
    $obLista = new Lista;
    $obLista->setRecordSet	( $rsLista				);
    $obLista->setTitulo		( "Resultados da Busca" );
    $obLista->setMostraPaginacao ( false 			);
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo	( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth    	( 5 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo	( "Número do Convênio" );
    $obLista->ultimoCabecalho->setWidth 	( 15 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo	( "Entidade" );
    $obLista->ultimoCabecalho->setWidth		( 5 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo	( "Objeto do Convênio" );
    $obLista->ultimoCabecalho->setWidth		( 40 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo	( "Valor" );
    $obLista->ultimoCabecalho->setWidth		( 15 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo 	( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth 	( 5 );
    $obLista->commitCabecalho ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento 	( "DIREITA" );
    $obLista->ultimoDado->setCampo 			( "[nro_convenio]/[exercicio]" );
    $obLista->commitDado ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento 	( "ESQUERDA" );
    $obLista->ultimoDado->setCampo 			( "cod_entidade" );
    $obLista->commitDado ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento 	( "ESQUERDA" );
    $obLista->ultimoDado->setCampo 			( "objeto" );
    $obLista->commitDado ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento	( "DIREITA" );
    $obLista->ultimoDado->setCampo			( "vl_convenio" );
    $obLista->commitDado ();
    if ($stAcao == 'alterar') {
        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao 	( "ALTERAR" );
        $obLista->ultimaAcao->addCampo	("inNumConvenio","nro_convenio");
        $obLista->ultimaAcao->addCampo	("inCodConvenio","cod_convenio");
        $obLista->ultimaAcao->addCampo	("inExercicio"  ,"exercicio"   );
        $obLista->ultimaAcao->addCampo	("inCodEntidade","cod_entidade");
        $obLista->ultimaAcao->setLink	( $pgForm."?".Sessao::getId().$stLink."&stAcao=alterar&" );
        $obLista->commitAcao();
    } elseif ($stAcao == 'excluir') {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao	( 'excluir' );
        $obLista->ultimaAcao->addCampo	("&inNumConvenio","nro_convenio");
        $obLista->ultimaAcao->addCampo	("&inCodConvenio","cod_convenio");
        $obLista->ultimaAcao->addCampo	("&stDescQuestao","nro_convenio");
        $obLista->ultimaAcao->addCampo	("&inExercicio"  ,"exercicio"   );
        $obLista->ultimaAcao->addCampo	("&inCodEntidade","cod_entidade");
        $obLista->ultimaAcao->setLink	( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&nomAcao=Excluir%20Convênio&stAcao=excluirConvenio" );
        $obLista->commitAcao();
    }
    $obLista->Show();
}

include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenio.class.php' );
$obTTCEMGConvenio = new TTCEMGConvenio;

$stExercicio = ($stExercicio!='') ? $stExercicio : Sessao::getExercicio();
$stFiltro = " WHERE convenio.exercicio = '".$stExercicio."'";

if ($inCodEntidade!='') {
    $stFiltro .= " AND convenio.cod_entidade = ".$inCodEntidade."";
}

if ($inNumConvenio!='') {
    $stFiltro .= " AND convenio.nro_convenio = ".$inNumConvenio."";
}

if ($dtInicial!='') {
    $stFiltro .= " AND convenio.data_inicio >= to_date('".$dtInicial."','dd/mm/yyyy')";
}

if ($dtFinal!='') {
    $stFiltro .= " AND convenio.data_final <= to_date('".$dtFinal."','dd/mm/yyyy')";
}

$stOrdem = "ORDER BY convenio.nro_convenio ASC";

$obTTCEMGConvenio->recuperaConvenioFiltro ( $rsConvenio , $stFiltro, $stOrdem);

montaListaAlteracao ( $rsConvenio , '' , $_REQUEST['stAcao'] );
?>