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
  * Página de
  * Data de criação : 25/10/2005

  * @copyright CCA Consultoria de Gestão Pública S/S Ltda.
  * @link http://www.ccanet.com.br CCA Consultoria de Gestão Pública S/S Ltda.

  * @author Analista:
  * @author Programador: Fernando Zank Correa Evangelista

  $Id: TPatrimonioDepreciacaoReavaliacao.class.php 43154 2009-11-20 11:16:13Z vitorhugo $

  Caso de uso: uc-03.01.09
  Caso de uso: uc-03.01.21

  **/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioDepreciacaoReavaliacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPatrimonioDepreciacaoReavaliacao()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.depreciacao_reavaliacao');
        $this->setComplementoChave('cod_depreciacao,cod_reavaliacao,cod_bem,timestamp');
        $this->AddCampo('cod_depreciacao','integer',true,'',true,false);
        $this->AddCampo('cod_reavaliacao','integer',true,'',true,false);
        $this->AddCampo('cod_bem','integer',true,'',true,false);
        $this->AddCampo('timestamp','timestamp',false,'',false,false);

    }
        
    public function recuperaReavaliacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaReavaliacao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaReavaliacao()
    {
        $stSql  = " SELECT reavaliacao.cod_reavaliacao
                         , reavaliacao.cod_bem
                         , depreciacao.cod_depreciacao
                      FROM patrimonio.depreciacao_reavaliacao

                INNER JOIN patrimonio.depreciacao
                        ON depreciacao_reavaliacao.cod_depreciacao = depreciacao.cod_depreciacao
                       AND depreciacao_reavaliacao.cod_bem         = depreciacao.cod_bem         
                       AND depreciacao_reavaliacao.timestamp       = depreciacao.timestamp       

                INNER JOIN patrimonio.reavaliacao
                        ON reavaliacao.cod_reavaliacao = depreciacao_reavaliacao.cod_reavaliacao
                       AND reavaliacao.cod_bem         = depreciacao_reavaliacao.cod_bem 

                     WHERE NOT EXISTS ( SELECT 1 
                                          FROM patrimonio.depreciacao_anulada
                                         WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                           AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                           AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                       ) \n ";
        return $stSql;
    }
    
    
    public function recuperaRelacaoDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRelacaoDepreciacao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRelacaoDepreciacao()
    {
        $stSql  = " SELECT depreciacao_reavaliacao.cod_depreciacao
                      FROM patrimonio.reavaliacao

                 LEFT JOIN patrimonio.depreciacao_reavaliacao
                        ON depreciacao_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao 
                       AND depreciacao_reavaliacao.cod_bem         = reavaliacao.cod_bem          \n ";
        
        return $stSql;
    }
    
}

?>
