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
/*
    * Página de Mapeamento - Exportação Arquivos TCM-BA- Prorrogparc.txt
    * Data de Criação:       02/10/2015
    * @author Analista:      Valtair Santos
    * @author Desenvolvedor: Evandro Melos
    * 
    * $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAProrrogParc extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaProrrogacaoParceria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaProrrogacaoParceria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao,$obConexao );
        return $obErro;
    }

    public function montaRecuperaProrrogacaoParceria()
    {
        $stSql = "  SELECT  1 AS tipo_registro
                            ,  ".$this->getDado('unidade_gestora')." AS unidade_gestora
                            , nro_processo
                            , '".$this->getDado('competencia')."' AS competencia
                            , dt_prorrogacao
                            , '' as reservado_tcm
                            , CASE WHEN indicador_adimplemento IS TRUE THEN
                                        1
                                    ELSE
                                        2
                             END as indicador_adimplemento
                            , nro_termo_aditivo
                            , dt_publicacao
                            , imprensa_oficial
                            , dt_inicio
                            , dt_termino
                            , vl_prorrogacao
                       FROM tcmba.termo_parceria_prorrogacao
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND dt_prorrogacao BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                                               AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy') 
            ";
        
        return $stSql;
    }
}
