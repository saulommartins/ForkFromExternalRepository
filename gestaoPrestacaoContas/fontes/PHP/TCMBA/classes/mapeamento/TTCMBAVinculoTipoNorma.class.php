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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAVinculoTipoNorma extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.vinculo_tipo_norma');
        $this->setCampoCod('cod_tipo');

        $this->AddCampo('cod_tipo_norma' , 'integer' , true  , '' , true  ,false);
        $this->AddCampo('cod_tipo'       , 'integer' , false , '' , true ,false);
    }

    public function recuperaVinculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVinculo().$stFiltro.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaVinculo()
    {
        $stSql  = " SELECT tipo_norma.cod_tipo_norma
                         , tipo_norma.nom_tipo_norma
                         , vinculo_tipo_norma.cod_tipo AS cod_tipo_norma_tcmba

                     FROM normas.tipo_norma

                LEFT JOIN tcmba.vinculo_tipo_norma
                       ON vinculo_tipo_norma.cod_tipo_norma = tipo_norma.cod_tipo_norma
        ";

        return $stSql;
    }

}
