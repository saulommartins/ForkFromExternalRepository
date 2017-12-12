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
    * Classe de mapeamento da tabela empenho.ordem_pagamento_assinatura
    * Data de Criação: 04/01/2008

    * @author Analista: Anderson cAko Konze
    * @author Desenvolvedor: Leopoldo Barreiro

    $Id: TEmpenhoOrdemPagamentoAssinatura.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoOrdemPagamentoAssinatura extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoOrdemPagamentoAssinatura()
{
    parent::Persistente();
    $this->setTabela("empenho.ordem_pagamento_assinatura");

    $this->setCampoCod('num_assinatura');
    $this->setComplementoChave('exercicio,cod_entidade,cod_ordem');

    $this->AddCampo('exercicio'     ,'char'    ,true  ,'4'  ,true,'TEmpenhoOrdemPagamento');
    $this->AddCampo('cod_entidade'  ,'integer' ,true  ,''   ,true,'TEmpenhoOrdemPagamento');
    $this->AddCampo('cod_ordem'     ,'integer' ,true  ,''   ,true,'TEmpenhoOrdemPagamento');
    $this->AddCampo('num_assinatura','sequence',true  ,''   ,true,false);
    //$this->AddCampo('numcgm'        ,'integer' ,true  ,''   ,false,'TPublicSwCgm');
    $this->AddCampo('numcgm'         ,'integer' ,true  ,''   ,false, 'TCGM');
    $this->AddCampo('cargo'         ,'varchar' ,true  ,'80' ,false,false);
    /*
    $this->AddCampo('GRANT'         ,'select,' ,false ,''   ,,false);
    $this->AddCampo('GRANT'         ,'select,' ,false ,''   ,,false);
    $this->AddCampo('GRANT'         ,'select,' ,false ,''   ,,false);
    $this->AddCampo('GRANT'         ,'select,' ,false ,''   ,,false);
    */
}

/**
    * Monta a SQL para recuperar as assinaturas de uma Ordem de Pagamento
    * @access Public
    * @param String (Exercicio), Integer (Cod Entidade), Integer (Cod Ordem Pagamento)
    * @return String SQL
*/
function montaRecuperaAssinaturasOrdem()
{
    $stSQL = "	SELECT 	opass.exercicio,
                        opass.cod_entidade,
                        opass.cod_ordem,
                        opass.num_assinatura,
                        scgm.numcgm,
                        scgm.nom_cgm,
                        opass.cargo
                FROM empenho.ordem_pagamento_assinatura AS opass
                JOIN sw_cgm AS scgm USING (numcgm)
                WHERE opass.exercicio = '" . $this->getDado('exercicio') . "'
                    AND opass.cod_entidade = " . $this->getDado('cod_entidade') . "
                    AND opass.cod_ordem = " . $this->getDado('cod_ordem') . " ";

    return $stSQL;
}

/**
    * Recupera assinaturas de uma Ordem de Pagamento
    * @access Public
    * @param Object RecordSet, String Filtro, String Order, Boolean Transação
    * @return Object RecordSet
*/
function recuperaAssinaturasOrdem(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaAssinaturasOrdem", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

/**
    * Retorna a equivalencia entre papel e num_assinatura do registro
    * @access Public
    * @param void
    * @return Array
*/
function arrayPapel()
{
    $arPapel = array( 'visto'=>1, 'ordenador'=>2, 'tesoureiro'=>3 );

    return $arPapel;
}

}
?>
