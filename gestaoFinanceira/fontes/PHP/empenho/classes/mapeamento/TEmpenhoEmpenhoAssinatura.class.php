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
    * Classe de mapeamento da tabela empenho.empenho_assinatura
    * Data de Criação: 04/01/2008

    * @author Analista: Anderson cAko Konze
    * @author Desenvolvedor: Leopoldo Barreiro

    $Id: TEmpenhoEmpenhoAssinatura.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoEmpenhoAssinatura extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenhoAssinatura()
{
    parent::Persistente();
    $this->setTabela("empenho.empenho_assinatura");

    $this->setCampoCod('num_assinatura');
    $this->setComplementoChave('exercicio,cod_entidade,cod_empenho');

    $this->AddCampo('exercicio'     ,'char'    ,true  ,'4'  ,true,'TEmpenhoEmpenho');
    $this->AddCampo('cod_entidade'  ,'integer' ,true  ,''   ,true,'TEmpenhoEmpenho');
    $this->AddCampo('cod_empenho'   ,'integer' ,true  ,''   ,true,'TEmpenhoEmpenho');
    $this->AddCampo('num_assinatura','sequence',true  ,''   ,true,false);
    //$this->AddCampo('numcgm'        ,'integer' ,true  ,''   ,false,'TPublicSwCgm');
    $this->AddCampo('numcgm'         ,'integer' ,true  ,''   ,false, 'TCGM');
    $this->AddCampo('cargo'         ,'varchar' ,true  ,'80' ,false,false);
}

/**
    * Monta a SQL para recuperar as assinaturas de uma Nota de Empenho
    * @access Public
    * @param String Exercicio, Integer Cod Entidade, Integer Cod Autorizacao
    * @return String SQL
*/
function montaRecuperaAssinaturasEmpenho()
{
    $stSQL = "	SELECT 	eea.exercicio,
                        eea.cod_entidade,
                        eea.cod_empenho,
                        eea.num_assinatura,
                        scgm.numcgm,
                        scgm.nom_cgm,
                        eea.cargo
                FROM empenho.empenho_assinatura AS eea
                JOIN sw_cgm AS scgm USING (numcgm)
                WHERE eea.exercicio = '" . $this->getDado('exercicio') . "'
                    AND eea.cod_entidade = " . $this->getDado('cod_entidade') . "
                    AND eea.cod_empenho = " . $this->getDado('cod_empenho') . " ";

    return $stSQL;
}

/**
    * Recupera assinaturas de uma Nota de Empenho
    * @access Public
    * @param Object RecordSet, String Filtro, String Order, Boolean Transação
    * @return Object RecordSet
*/
function recuperaAssinaturasEmpenho(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaAssinaturasEmpenho", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

/**
    * Retorna a equivalencia entre papel e num_assinatura do registro
    * @access Public
    * @param void
    * @return Array
*/
function arrayPapel()
{
    $arPapel = array( 'ordenador'=>1, 'conferido'=>2, 'contador'=>3, 'paguese'=>4 );

    return $arPapel;
}

}
?>
