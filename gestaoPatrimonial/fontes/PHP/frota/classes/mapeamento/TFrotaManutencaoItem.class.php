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
    * Data de Criação: 29/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: TFrotaManutencaoItem.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaManutencaoItem extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaManutencaoItem()
    {
        parent::Persistente();
        $this->setTabela('frota.manutencao_item');
        $this->setCampoCod('cod_manutencao');
        $this->setComplementoChave('exercicio,cod_item');
        $this->AddCampo('cod_manutencao','integer',true,'',true,true);
        $this->AddCampo('exercicio','varchar',true,'"4"',true,true);
        $this->AddCampo('cod_item','integer',true,'',true,true);
        $this->AddCampo('quantidade','numeric',true,'14.2',false,true);
        $this->AddCampo('valor','numeric',true,'14.2',false,true);
    }

    public function recuperaManutencaoItens(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaManutencaoItens",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaManutencaoItens()
    {
        $stSql = "
            SELECT manutencao_item.cod_manutencao
                 , manutencao_item.cod_item
                 , catalogo_item.descricao
                 , tipo_item.cod_tipo
                 , tipo_item.descricao AS nom_tipo
                 , manutencao_item.quantidade
                 , manutencao_item.valor
                 , CASE WHEN autorizacao.cod_autorizacao IS NULL
                        THEN true
                        ELSE ( CASE WHEN( autorizacao.quantidade = 0 OR autorizacao.valor = 0 )
                                    THEN true
                                    ELSE false
                               END )
                   END AS alteravel
              FROM frota.manutencao_item
        INNER JOIN frota.item
                ON item.cod_item = manutencao_item.cod_item
        INNER JOIN almoxarifado.catalogo_item
                ON catalogo_item.cod_item = item.cod_item
        INNER JOIN frota.tipo_item
                ON tipo_item.cod_tipo = item.cod_tipo
         LEFT JOIN frota.efetivacao
                ON efetivacao.cod_manutencao = manutencao_item.cod_manutencao
               AND efetivacao.exercicio_manutencao = manutencao_item.exercicio
         LEFT JOIN frota.autorizacao
                ON autorizacao.cod_autorizacao = efetivacao.cod_autorizacao
               AND autorizacao.exercicio = efetivacao.exercicio_autorizacao
             WHERE ";
        if ( $this->getDado('cod_manutencao') != '' ) {
            $stSql .= " manutencao_item.cod_manutencao = '".$this->getDado('cod_manutencao')."' AND   ";
        }
        if ( $this->getDado('exercicio') != '' ) {
            $stSql .= " manutencao_item.exercicio = '".$this->getDado('exercicio')."' AND   ";
        }

        return substr($stSql,0,-6);
    }
}
