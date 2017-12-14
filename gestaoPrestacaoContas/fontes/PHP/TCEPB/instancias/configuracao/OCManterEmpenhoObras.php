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
    * Pagina Oculta para Formulário
    * Data de Criação   : 26/04/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 22245 $
    $Name$
    $Autor:$
    $Date: 2007-04-27 15:31:00 -0300 (Sex, 27 Abr 2007) $

    * Casos de uso: uc-06.03.00

*/

/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(TTPB."TTPBObras.class.php");
include_once(TTPB."TTPBEmpenhoObras.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenhoObras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ?  $_REQUEST['stCtrl'] : $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case "BuscaObras":
        $obTObras = new TTPBObras();
        $obTObras->recuperaTodos($rsRecordSet, " WHERE exercicio=".$_REQUEST['inExercicioObra']." ORDER BY num_obra");

        $stNomeCombo = "inCodObra";
        $stJs .= "limpaSelect(f.".$stNomeCombo.",0); \n";
        $stJs .= "f.".$stNomeCombo.".options[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsRecordSet->eof() ) {
            $stCodigo = $rsRecordSet->getCampo( "num_obra" );
            $stDescr  = $rsRecordSet->getCampo( "num_obra" )." - ";
            $stDescr .= $rsRecordSet->getCampo( "descricao");
            $stJs .= "f.".$stNomeCombo.".options[$inContador] = ";
            $stJs .= "new Option('".$stDescr."','".$stCodigo."','".$stSelected."'); \n";
            $inContador++;
            $rsRecordSet->proximo();
        }
    echo $stJs;
    break;
    case "Lista":
        $obPersistente = new TTPBEmpenhoObras();
        $obPersistente->recuperaLista($rsRecordSet);
        $obLista = new Lista();
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo('Lista de Empenhos e Obras');
        $obLista->setRecordSet($rsRecordSet);
        //Cabeçalhos
        $obLista->addCabecalho('', 5);
        $obLista->addCabecalho('Entidade', 20);
        $obLista->addCabecalho('Empenho', 15);
        $obLista->addCabecalho('Obra', 25);
        $obLista->addCabecalho('Ação', 5);
        //Dados
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('[cod_entidade]-[nom_cgm]');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('[cod_empenho]');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->ultimoDado->setCampo('[num_obra]-[descricao]');
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( 'excluir' );
        $obLista->ultimaAcao->addCampo("&inExercicioEmpenho", "exercicio_empenho"  );
        $obLista->ultimaAcao->addCampo("&inCodEntidade", "cod_entidade"  );
        $obLista->ultimaAcao->addCampo("&inCodEmpenho", "cod_empenho"  );
        $obLista->ultimaAcao->addCampo("&inExercicioObras", "exercicio_obras"  );
        $obLista->ultimaAcao->addCampo("&inNumObra" , "num_obra" );
        $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"Empenho [cod_empenho]/[exercicio_empenho]");
        $obLista->ultimaAcao->setLink( CAM_GPC_TPB_INSTANCIAS.'configuracao/'.$pgProc."?stAcao=excluir&".Sessao::getId().$stLink );
        $obLista->commitAcao();

        $obLista->montaInnerHTML();

        $stJs .= "d.getElementById('spnLista').innerHTML='".$obLista->getHTML()."';";
        SistemaLegado::executaFrameOculto( $stJs );

    break;
}
?>
