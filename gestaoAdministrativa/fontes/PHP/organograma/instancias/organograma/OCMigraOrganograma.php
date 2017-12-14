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
    * Página de Oculto para Migrar Organograma
    * Data de criação : 05/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

include_once CAM_GA_ORGAN_NEGOCIO.'ROrganogramaOrganograma.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TMigraOrganograma.class.php';

$stPrograma = "MigraOrganograma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaTabelaMigracao()
{
    # Monta o RecordSet com o Organograma Padrão (antigo).
    $obTMigraOrganograma = new TMigraOrganograma;
    $obTMigraOrganograma->recuperaOrganogramaPadrao($rsOrganogramaPadrao);

    # Monta o RecordSet com os órgãos do Organograma Novo.
    $obROrganograma = new ROrganogramaOrganograma;

    # Recupera o id do Organograma Ativo.
    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.organograma', ' WHERE ativo = true');

    $obROrganograma->setCodOrganograma($inCodOrganograma);
    $obErro = $obROrganograma->listarNiveis($rsListaNivel);

    $table = new TableTree;
    $table->setRecordset($rsOrganogramaPadrao);
    $table->setSummary('Migrar Organograma');
    //$table->setConditional(true);
    $table->setArquivo('OCMigraOrganograma.php');
    $table->setParametros(array( "nom_orgao", "nom_unidade", "nom_departamento", "nom_setor"));
    $table->setComplementoParametros("stCtrl=montaEstruturaOrganograma");

    $table->Head->addCabecalho( 'Organograma Antigo (Setor)' , 60 );
    $table->Head->addCabecalho( 'Organograma Atual (Setor)'  , 40 );

    $table->Body->addCampo ('[cod_orgao].[cod_unidade].[cod_departamento].[cod_setor] - [nom_setor]' , 'L');

    $arOrgaos = $arAux = $arElementos = array();

    # Percorre o RecordSet para preencher o array com todos os órgãos.
    while (!$rsListaNivel->eof()) {
        $obROrganograma->obRNivel->setCodNivel($rsListaNivel->getCampo("cod_nivel"));
        $obROrganograma->listarOrgaosRelacionadosDescricao($rsListaOrgaos, "ORDER BY orgao ASC");

        $arOrgaos[] = $rsListaOrgaos->arElementos;

        $rsListaNivel->proximo();
    }

    # Unifica o array com todos os elementos, mantendo a sequência das chaves.
    foreach ($arOrgaos as $key => $value) {
        foreach ($value as $k => $v) {
            $arAux[] = $v;
        }
    }

    function ordenaChaveInterna($a, $b)
    {
        return (strcmp($a['orgao'],$b['orgao']));
    }

    # Ordena o array pela chave interna [orgao].
    uasort($arAux, 'ordenaChaveInterna');

    # Recria o array ordenado por órgão, porém respeitando as chaves sequenciais.
    foreach ($arAux as $key => $value) {
        $arElementos[] = $value;
    }

    $rsListaOrgaos->preenche($arElementos);

    $obCmbOrganograma = new Select;
    $obCmbOrganograma->setName      ('inCodOrgao_[ano_exercicio]_[cod_orgao]_[cod_unidade]_[cod_departamento]_[cod_setor]');
    $obCmbOrganograma->setValue     ('[cod_orgao_organograma]');
    $obCmbOrganograma->setCampoId   ('[cod_orgao]');
    $obCmbOrganograma->setCampoDesc ('[orgao] - [descricao]');
    $obCmbOrganograma->setStyle     ('width:300px; height: 25px;');
    $obCmbOrganograma->addOption    ('', 'Selecione', 'selected');
    $obCmbOrganograma->preencheCombo($rsListaOrgaos);

    # Adiciona o componente Select com todos os órgãos disponíveis.
    $table->Body->addComponente($obCmbOrganograma, 'ok');

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    # Monta a tabela para a migração do Organograma.
    $stJs  = "jQuery('#spnTable').html('".$stHTML."'); \n";
    $stJs .= "jQuery('#ok').attr('disabled', '');      \n";

    return $stJs;
}

function montaEstruturaOrganograma($arDados)
{
    $obFormulario = new Formulario;
    $obFormulario->addForm(null);

    $obLblOrgao = new Label;
    $obLblOrgao->setRotulo('Órgão');
    $obLblOrgao->setValue($arDados['nom_orgao']);

    $obLblUnidade = new Label;
    $obLblUnidade->setRotulo('Unidade');
    $obLblUnidade->setValue($arDados['nom_unidade']);

    $obLblDepartamento = new Label;
    $obLblDepartamento->setRotulo('Departamento');
    $obLblDepartamento->setValue($arDados['nom_departamento']);

    $obLblSetor = new Label;
    $obLblSetor->setRotulo('Setor');
    $obLblSetor->setValue($arDados['nom_setor']);

    $obFormulario->addTitulo('Estrutura Antiga do Organograma');
    $obFormulario->addComponente($obLblOrgao);
    $obFormulario->addComponente($obLblUnidade);
    $obFormulario->addComponente($obLblDepartamento);
    $obFormulario->addComponente($obLblSetor);

    $obFormulario->montaHTML();
    $stHTML = $obFormulario->getHtml();

    return $stHTML;
}

switch ($stCtrl) {
    case 'montaTabelaMigracao':
        $stJs = montaTabelaMigracao();
    break;

    case 'montaEstruturaOrganograma':
        echo montaEstruturaOrganograma($_REQUEST);
    break;

}

echo $stJs;
