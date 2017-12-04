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
    * Arquivo de mapeamento para a função que busca os dados da ConsDesExtOrc
    * 
    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    * $Id: TTCMBAConsDesExtOrc.class.php 63361 2015-08-20 20:16:30Z franver $
    * $Rev: 63361 $
    * $Author: franver $
    * $Date: 2015-08-20 17:16:30 -0300 (Qui, 20 Ago 2015) $
    * 
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAConsDesExtOrc extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaDadosTribunal()
    {
        $stSql = "
                  SELECT 1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       , ".$this->getDado('exercicio')."::VARCHAR||".$this->getDado('mes')."::VARCHAR AS competencia
                       , REPLACE(conta_contabil,'.','') AS conta_contabil
                       , SUM(COALESCE(vl_mes,0.00)) AS vl_mes
                       , SUM(COALESCE(vl_ate_mes,0.00)) AS vl_ate_mes
                    FROM tcmba.despesaExtraOrcamentaria('".$this->getDado('exercicio')."'
                                                       ,'".$this->getDado('entidades')."'
                                                       ,'".$this->getDado('dt_inicial')."'
                                                       ,'".$this->getDado('dt_final')."'
                                                       ,'".$this->getDado('dt_inicial_mes')."')
                GROUP BY tipo_registro
                       , unidade_gestora
                       , competencia
                       , conta_contabil
                ORDER BY conta_contabil
        ";
        return $stSql;
    }
    
    public function __destruct() {}

}
