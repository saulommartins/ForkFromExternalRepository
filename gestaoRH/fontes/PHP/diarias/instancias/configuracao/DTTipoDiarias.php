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
    * Página de Detalhamento de Tabela para Configuração de Tipos de Diárias
    * Data de Criação: 11/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: DTTipoDiarias.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.09.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php"                                    );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$rsTipoDiaria = new RecordSet();

if ($_REQUEST['inCodTipo'] != "") {
    $stFiltroDiaria  = " AND tipo_diaria.cod_tipo = ".$_REQUEST['inCodTipo']."\n";

    $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
    $obTDiariasTipoDiaria->recuperaHistorico($rsTipoDiaria, $stFiltroDiaria);
}

$obTableTree = new Table();
$obTableTree->setSummary("Histórico de Alterações");

if( $_REQUEST['inCodTipo'] == "" ||
    $rsTipoDiaria->getNumLinhas() < 1){

    $rsTipoDiaria->add(array('mensagem' => "Não foram encontradas alterações para o registro"));
    $obTableTree->setRecordset($rsTipoDiaria);

    $obTableTree->Head->addCabecalho( 'Mensagem' , 70  );
    $obTableTree->Body->addCampo( 'mensagem', 'C' );

} else {

    $obTableTree->setRecordset($rsTipoDiaria);

    $obTableTree->Head->addCabecalho( 'Nome' , 30  );
    $obTableTree->Head->addCabecalho( 'Lei/Decreto' , 10  );
    $obTableTree->Head->addCabecalho( 'Data Publicação' , 10  );
    $obTableTree->Head->addCabecalho( 'Valor' , 10  );
    $obTableTree->Head->addCabecalho( 'Rubrica Despesa' , 20  );

    $obTableTree->Body->addCampo( 'nom_tipo', 'E' );
    $obTableTree->Body->addCampo( 'num_norma_exercicio', 'E' );
    $obTableTree->Body->addCampo( 'dt_publicacao_norma', 'E' );
    $obTableTree->Body->addCampo( 'R$ [valor]', 'D' );
    $obTableTree->Body->addCampo( 'mascara_classificacao', 'E' );

}

$obTableTree->montaHTML();
echo $obTableTree->getHtml();

?>
