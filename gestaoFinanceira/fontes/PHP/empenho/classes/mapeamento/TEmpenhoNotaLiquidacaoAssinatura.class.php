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
    * Classe de mapeamento da tabela empenho.nota_liquidacao_assinatura
    * Data de Criação: 23/07/2008

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Eduardo Schitz

    $Id: TEmpenhoNotaLiquidacaoAssinatura.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoNotaLiquidacaoAssinatura extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoNotaLiquidacaoAssinatura()
{
    $sessao = $_SESSION ['sessao'];
    parent::Persistente();
    $this->setTabela("empenho.nota_liquidacao_assinatura");

    $this->setCampoCod('num_assinatura');
    $this->setComplementoChave('exercicio,cod_entidade,cod_nota');

    $this->AddCampo('exercicio'     ,'char'    ,true  ,'4'  ,true,'TEmpenhoNotaLiquidacao');
    $this->AddCampo('cod_entidade'  ,'integer' ,true  ,''   ,true,'TEmpenhoNotaLiquidacao');
    $this->AddCampo('cod_nota'   ,'integer' ,true  ,''   ,true,'TEmpenhoNotaLiquidacao');
    $this->AddCampo('num_assinatura','sequence',true  ,''   ,true,false);
    $this->AddCampo('numcgm'         ,'integer' ,true  ,''   ,false, 'TCGM');
    $this->AddCampo('cargo'         ,'varchar' ,true  ,'80' ,false,false);
}

/**
    * Monta a SQL para recuperar as assinaturas de uma Nota de Liquidacao
    * @access Public
    * @param String Exercicio, Integer Cod Entidade, Integer Cod Autorizacao
    * @return String SQL
*/
function montaRecuperaAssinaturasNotaLiquidacao()
{
    $stSQL = "  SELECT  enla.exercicio,
                        enla.cod_entidade,
                        enla.cod_nota,
                        scgm.numcgm,
                        scgm.nom_cgm,
                        enla.cargo
                FROM empenho.nota_liquidacao_assinatura AS enla
                JOIN sw_cgm AS scgm USING (numcgm)
                WHERE enla.exercicio = '" . $this->getDado('exercicio') . "'
                    AND enla.cod_entidade = " . $this->getDado('cod_entidade') . "
                    AND enla.cod_nota = " . $this->getDado('cod_nota') . " ";

    return $stSQL;
}

/**
    * Recupera assinaturas de uma Nota de Liquidacao
    * @access Public
    * @param Object RecordSet, String Filtro, String Order, Boolean Transação
    * @return Object RecordSet
*/
function recuperaAssinaturasNotaLiquidacao(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaAssinaturasNotaLiquidacao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

}
?>
