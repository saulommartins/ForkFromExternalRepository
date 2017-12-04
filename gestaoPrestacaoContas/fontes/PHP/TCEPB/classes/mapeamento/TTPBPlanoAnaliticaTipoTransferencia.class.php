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
    * Classe de mapeamento da tabela
    * Data de Criação: 23/09/2014

    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBPlanoAnaliticaTipoTransferencia.class.php 59999 2014-09-24 20:16:27Z silvia $

    * Casos de uso:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBPlanoAnaliticaTipoTransferencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TTPBPlanoAnaliticaTipoTransferencia()
    {
        parent::Persistente();
        $this->setTabela("tcepb.plano_conta_tipo_transferencia");
        
        $this->setCampoCod('cod_conta');
        $this->setComplementoChave('exercicio');
        
        $this->AddCampo( 'cod_conta'    ,'integer'  ,true, ''   ,true   ,true );
        $this->AddCampo( 'exercicio'    ,'char'     ,true, '4'  ,true   ,true );
        $this->AddCampo( 'cod_tipo'     ,'integer'  ,true, ''   ,false  ,true );
    }
    
    function recuperaContaTransferenciaRecebida(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContaTransferenciaRecebida().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaContaTransferenciaRecebida()
    {
        $stSql = " SELECT                                                   
                          plano_analitica.cod_plano,                                        
                          plano_conta.exercicio,                                        
                          plano_conta.cod_conta,                                        
                          plano_conta.nom_conta,                                        
                          plano_conta.cod_estrutural,                                  
                          plano_conta_tipo_transferencia.cod_tipo AS cod_tipo_transferencia
                         
                     FROM                                                     
                          contabilidade.plano_conta
                         
                     JOIN                 
                          contabilidade.plano_analitica
                       ON plano_conta.cod_conta  = plano_analitica.cod_conta 
                      AND plano_conta.exercicio  = plano_analitica.exercicio    
                   
                LEFT JOIN
                          tcepb.plano_conta_tipo_transferencia
                       ON tcepb.plano_conta_tipo_transferencia.cod_conta = plano_analitica.cod_conta
                      AND tcepb.plano_conta_tipo_transferencia.exercicio = plano_analitica.exercicio
                   
                    WHERE                            
                          plano_conta.cod_conta  = plano_analitica.cod_conta 
                      AND plano_conta.exercicio  = plano_analitica.exercicio
                      AND (plano_conta.cod_estrutural ilike '4.5.1.3.2.%' OR plano_conta.cod_estrutural ilike '4.5.1.2.2.01.%')
                      AND plano_conta.exercicio = '".Sessao::getExercicio()."'
                ";
    
        return $stSql;
    }
    
    function recuperaContaTransferenciaConcedidas(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContaTransferenciaConcedidas().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaContaTransferenciaConcedidas()
    {
        $stSql = " SELECT                                                   
                          plano_analitica.cod_plano,                                        
                          plano_conta.exercicio,                                        
                          plano_conta.cod_conta,                                        
                          plano_conta.nom_conta,                                        
                          plano_conta.cod_estrutural,                                  
                          plano_conta_tipo_transferencia.cod_tipo AS cod_tipo_transferencia
                         
                     FROM                                                     
                          contabilidade.plano_conta
                         
                     JOIN                 
                          contabilidade.plano_analitica
                       ON plano_conta.cod_conta  = plano_analitica.cod_conta 
                      AND plano_conta.exercicio  = plano_analitica.exercicio    
                   
                LEFT JOIN
                          tcepb.plano_conta_tipo_transferencia
                       ON tcepb.plano_conta_tipo_transferencia.cod_conta = plano_analitica.cod_conta
                      AND tcepb.plano_conta_tipo_transferencia.exercicio = plano_analitica.exercicio
                   
                    WHERE                            
                          plano_conta.cod_conta  = plano_analitica.cod_conta 
                      AND plano_conta.exercicio  = plano_analitica.exercicio
                      AND (plano_conta.cod_estrutural ilike '3.5.1.2.2.01%' OR plano_conta.cod_estrutural ilike '3.5.1.3.2.%')
                      AND plano_conta.exercicio = '".Sessao::getExercicio()."'
                ";
    
        return $stSql;
    }
    
    function recuperaTipoTransferencia(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaTipoTransferencia().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaTipoTransferencia()
    {
        $stSql = " SELECT * FROM tcepb.tipo_transferencia ";
    
        return $stSql;
    }
    
}
