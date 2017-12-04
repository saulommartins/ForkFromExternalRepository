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
 * Classe de mapeamento da tabela compras.mapa_item
 * Data de Criação: 30/06/2006

 * @author Analista: Gelsom Kolowski
 * @author Desenvolvedor: Bruce Cruz de Sena

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-03.04.05
                 uc-03.05.26

 $Id: TComprasMapaItemReserva.class.php 63738 2015-10-02 17:54:55Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TComprasMapaItemReserva extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("compras.mapa_item_reserva");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio_mapa, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa');

        $this->AddCampo('exercicio_mapa'        ,'char'    ,true ,'4',true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_mapa'              ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('exercicio_solicitacao' ,'char'    ,true ,'4',true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_entidade'          ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_solicitacao'       ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_centro'            ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_item'              ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('lote'                  ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_conta'             ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_despesa'           ,'integer' ,true ,'' ,true  ,true , 'TComprasMapaItemDotacao');
        $this->AddCampo('cod_reserva'           ,'integer' ,true ,'' ,false ,true , 'TOrcamentoReservaSaldos');
        $this->AddCampo('exercicio_reserva'     ,'char'    ,true ,'4',false ,true , 'TOrcamentoReservaSaldos');
    }

    public function recuperaReservas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {

        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaReservas().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaReservas()
    {
        $stSql  = "      SELECT 																								  \n";
        $stSql .= "              mapa_item_reserva.exercicio_reserva                                                              \n";
        $stSql .= "			  ,  mapa_item_reserva.cod_reserva                                                                    \n";
        $stSql .= "			  ,  mapa_item_reserva.cod_despesa                                                                    \n";
        $stSql .= "			  ,  mapa_item_reserva.cod_conta                                                                      \n";
        $stSql .= "			  ,  mapa_item_reserva.cod_item                                                                       \n";
        $stSql .= "			  ,  mapa_item_reserva.cod_centro                                                                     \n";
        $stSql .= "			  ,  mapa_item_reserva.cod_entidade                                                                   \n";
        $stSql .= "	                                                                                                              \n";
        $stSql .= "			  ,  solicitacao_homologada_reserva.cod_reserva as cod_reserva_solicitacao                            \n";
        $stSql .= "			  ,  solicitacao_homologada_reserva.exercicio   as exercicio_reserva_solicitacao                      \n";
        $stSql .= "			  ,  COALESCE(reserva_saldos_solicitacao.vl_reserva, 0.00) as vl_reserva_solicitacao                  \n";
        $stSql .= "	                                                                                                              \n";
        $stSql .= "			  ,  reserva_saldos.vl_reserva                                                                        \n";
        $stSql .= "                                                                                                               \n";
        $stSql .= "		   FROM  compras.mapa_item_reserva                                                                        \n";
        $stSql .= "                                                                                                               \n";
        $stSql .= "	 INNER JOIN  orcamento.reserva_saldos                                                                         \n";
        $stSql .= "			 ON  mapa_item_reserva.cod_reserva       = reserva_saldos.cod_reserva                                 \n";
        $stSql .= "			AND  mapa_item_reserva.exercicio_reserva = reserva_saldos.exercicio                                   \n";
        $stSql .= "                                                                                                               \n";
        $stSql .= "   LEFT JOIN  compras.solicitacao_homologada_reserva                                                           \n";
        $stSql .= " 	     ON  mapa_item_reserva.exercicio_solicitacao = solicitacao_homologada_reserva.exercicio               \n";
        $stSql .= " 	    AND  mapa_item_reserva.cod_solicitacao       = solicitacao_homologada_reserva.cod_solicitacao         \n";
        $stSql .= " 	    AND  mapa_item_reserva.cod_entidade          = solicitacao_homologada_reserva.cod_entidade            \n";
        $stSql .= " 	    AND  mapa_item_reserva.cod_centro            = solicitacao_homologada_reserva.cod_centro              \n";
        $stSql .= " 	    AND  mapa_item_reserva.cod_item              = solicitacao_homologada_reserva.cod_item                \n";
        $stSql .= " 	    AND  mapa_item_reserva.cod_conta             = solicitacao_homologada_reserva.cod_conta               \n";
        $stSql .= " 	    AND  mapa_item_reserva.cod_despesa           = solicitacao_homologada_reserva.cod_despesa             \n";
        $stSql .= "                                                                                                               \n";
        $stSql .= "   LEFT JOIN  (                                                                                                \n";
        $stSql .= "     			SELECT  *                                                                                     \n";
        $stSql .= "     			  FROM  orcamento.reserva_saldos                                                              \n";
        $stSql .= "   	    		 WHERE  NOT EXISTS (                                                                          \n";
        $stSql .= "   		  							SELECT  1                                                                 \n";
        $stSql .= "   								  	  FROM  orcamento.reserva_saldos_anulada                                  \n";
        $stSql .= "   								     WHERE  reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva   \n";
        $stSql .= "   								  	   AND  reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio     \n";
        $stSql .= "   			   				       )                                                                          \n";
        $stSql .= "   		     ) as reserva_saldos_solicitacao                                                                  \n";
        $stSql .= "   	     ON  solicitacao_homologada_reserva.cod_reserva = reserva_saldos_solicitacao.cod_reserva              \n";
        $stSql .= "   	    AND  solicitacao_homologada_reserva.exercicio   = reserva_saldos_solicitacao.exercicio                \n";

        return $stSql;
    }

    public function recuperaMapaItemReserva(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMapaItemReserva",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMapaItemReserva()
    {
            $stSql = "
            SELECT  mapa_item.cod_solicitacao
                 ,  mapa_item.exercicio
              FROM  compras.mapa_item
        INNER JOIN  compras.mapa_solicitacao
                ON  mapa_solicitacao.exercicio = mapa_item.exercicio
               AND  mapa_solicitacao.cod_entidade = mapa_item.cod_entidade
               AND  mapa_solicitacao.cod_solicitacao = mapa_item.cod_solicitacao
               AND  mapa_solicitacao.cod_mapa = mapa_item.cod_mapa
               AND  mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
        INNER JOIN  compras.solicitacao
                ON  solicitacao.exercicio = mapa_solicitacao.exercicio_solicitacao
               AND  solicitacao.cod_entidade = mapa_solicitacao.cod_entidade
               AND  solicitacao.cod_solicitacao = mapa_solicitacao.cod_solicitacao  
             WHERE
        NOT EXISTS  (
                        SELECT  1
                          FROM  compras.mapa_item_reserva
                     LEFT JOIN  orcamento.reserva_saldos_anulada
                            ON  reserva_saldos_anulada.cod_reserva  = mapa_item_reserva.cod_reserva
                           AND  reserva_saldos_anulada.exercicio    = mapa_item_reserva.exercicio_reserva
                         WHERE  mapa_item_reserva.exercicio_mapa  = mapa_item.exercicio
                           AND  mapa_item_reserva.cod_entidade    = mapa_item.cod_entidade
                           AND  mapa_item_reserva.cod_solicitacao = mapa_item.cod_solicitacao
                           AND  mapa_item_reserva.cod_centro      = mapa_item.cod_centro
                           AND  mapa_item_reserva.cod_item        = mapa_item.cod_item
                           AND  mapa_item_reserva.cod_mapa        = mapa_item.cod_mapa 
                           AND  mapa_item_reserva.exercicio_mapa  = mapa_item.exercicio
                           AND  reserva_saldos_anulada.cod_reserva IS NULL
                    )
               AND  mapa_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')."
               AND  mapa_item.cod_entidade    = ".$this->getDado('cod_entidade')."
               AND  mapa_item.exercicio       = '".$this->getDado('exercicio')."'
               AND  mapa_item.cod_mapa        = ".$this->getDado('cod_mapa')."
               AND  solicitacao.registro_precos = FALSE
            ";

        return $stSql;
    }

    public function recuperaMapaReserva(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMapaReserva",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMapaReserva()
    {
        $stSql = "
            SELECT  *
              FROM  compras.mapa
             WHERE
            EXISTS  (
                        SELECT 	1
                          FROM 	compras.mapa_item
                    INNER JOIN  compras.mapa_item_reserva
                            ON 	mapa_item.exercicio = mapa_item_reserva.exercicio_mapa
                           AND 	mapa_item.cod_mapa = mapa_item_reserva.cod_mapa
                           AND 	mapa_item.exercicio_solicitacao = mapa_item_reserva.exercicio_solicitacao
                           AND 	mapa_item.cod_entidade = mapa_item_reserva.cod_entidade
                           AND 	mapa_item.cod_solicitacao = mapa_item_reserva.cod_solicitacao
                           AND 	mapa_item.cod_centro = mapa_item_reserva.cod_centro
                           AND 	mapa_item.cod_item = mapa_item_reserva.cod_item
                           AND 	mapa_item.lote = mapa_item_reserva.lote
                           AND 	mapa_item.cod_mapa = mapa.cod_mapa
                           AND 	mapa_item.exercicio = mapa.	exercicio
                    )
               AND  mapa.cod_mapa = ".$this->getDado('cod_mapa')."
               AND  mapa.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }
    
    public function __destruct() {}
}

?>
