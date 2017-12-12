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
    * Classe de mapeamento da tabela tesouraria.recibo_extra_assinatura
    * Data de Criação: 04/01/2008

    * @author Analista: Anderson cAko Konze
    * @author Desenvolvedor: Leopoldo Barreiro

    $Id: TTesourariaReciboExtraAssinatura.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaReciboExtraAssinatura extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaReciboExtraAssinatura()
{
    parent::Persistente();
    $this->setTabela("tesouraria.recibo_extra_assinatura");
    $this->setCampoCod('num_assinatura');
    $this->setComplementoChave('exercicio,cod_entidade,tipo_recibo,cod_recibo_extra');
    $this->AddCampo('exercicio'       ,'char'    ,true  ,'4'  ,true,'TTesourariaReciboExtra');
    $this->AddCampo('cod_entidade'    ,'integer' ,true  ,''   ,true,'TTesourariaReciboExtra');
    $this->AddCampo('tipo_recibo'     ,'char'    ,true  ,'1'  ,true,'TTesourariaReciboExtra');
    $this->AddCampo('cod_recibo_extra','integer' ,true  ,''   ,true,'TTesourariaReciboExtra');
    $this->AddCampo('num_assinatura'  ,'sequence',true  ,''   ,true,false);
    //$this->AddCampo('numcgm'          ,'integer' ,true  ,''   ,false,'TPublicSwCgm');
    $this->AddCampo('numcgm'         ,'integer' ,true  ,''   ,false, 'TCGM');
    $this->AddCampo('cargo'           ,'varchar' ,true  ,'80' ,false,false);
}

/**
    * Monta a SQL para recuperar as assinaturas de uma Ordem de Pagamento
    * @access Public
    * @param String (Exercicio), Integer (Cod Entidade), Integer (Cod Ordem Pagamento)
    * @return String SQL
*/
function montaRecuperaAssinaturasReciboExtra()
{
    $stSQL = "	SELECT 	trea.exercicio,
                        trea.cod_entidade,
                        trea.cod_recibo_extra,
                        trea.tipo_recibo,
                        trea.num_assinatura,
                        scgm.numcgm,
                        scgm.nom_cgm,
                        trea.cargo
                FROM tesouraria.recibo_extra_assinatura AS trea
                JOIN sw_cgm AS scgm USING (numcgm)
                WHERE trea.exercicio = '" . $this->getDado('exercicio') . "'
                    AND trea.cod_entidade = " . $this->getDado('cod_entidade') . "
                    AND trea.cod_recibo_extra = " . $this->getDado('cod_recibo_extra') . "
                    AND trea.tipo_recibo = '" . $this->getDado('tipo_recibo') . "' ";

    return $stSQL;
}

/**
    * Recupera assinaturas de uma Ordem de Pagamento
    * @access Public
    * @param Object RecordSet, String Filtro, String Order, Boolean Transação
    * @return Object RecordSet
*/
function recuperaAssinaturasReciboExtra(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaAssinaturasReciboExtra", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

/**
    * Retorna a equivalencia entre papel e num_assinatura do registro
    * @access Public
    * @param void
    * @return Array
*/
function arrayPapel()
{
    if ($this->getDado('tipo_recibo') == 'R') {
        $arPapel = array( 'tesoureiro'=>1 );
    } else {
        $arPapel = array( 'conferido'=>1, 'contador'=>2, 'ordenador'=>3, 'tesoureiro'=>4 );
    }

    return $arPapel;
}

}
?>
