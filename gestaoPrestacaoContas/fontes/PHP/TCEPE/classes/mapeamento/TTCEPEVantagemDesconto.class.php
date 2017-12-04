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

    * 
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEUnidadeOrcamentaria.class.php 60204 2014-10-06 20:47:57Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEVantagemDesconto extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEVantagemDesconto()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaUnidadeOrcamentaria.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaVantagemDesconto(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaVantagemDesconto().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaVantagemDesconto()
    { 
        $stSql = "SELECT reservado_tce
                       , matricula
                       , cod_cargo
                       , cpf
                       , mes_folha
                       , ano_folha
                       , justificativa
                       , tipo_folha
                       , SUM(vl_vantdesc) AS vl_vantdesc
                       , cod_vantdesc
                    FROM tcepe.recupera_vantagem_desconto ('".$this->getDado('stDataInicial')."', '".$this->getDado('stDataFinal')."', ".$this->getDado('inCodPeriodoMovimentacao').", ".$this->getDado('inCodComplementar').", '".$this->getDado('stEntidade')."' )
                      AS retorno ( reservado_tce INTEGER
                                 , matricula INTEGER
                                 , cod_cargo INTEGER
                                 , cpf VARCHAR
                                 , mes_folha TEXT
                                 , ano_folha TEXT
                                 , justificativa VARCHAR
                                 , tipo_folha INTEGER
                                 , vl_vantdesc NUMERIC
                                 , cod_vantdesc INTEGER
                                 )
                GROUP BY reservado_tce
                       , matricula
                       , cod_cargo
                       , cpf
                       , mes_folha
                       , ano_folha
                       , justificativa
                       , tipo_folha
                       , cod_vantdesc
        ";
                
        return $stSql;
    }

}
?>