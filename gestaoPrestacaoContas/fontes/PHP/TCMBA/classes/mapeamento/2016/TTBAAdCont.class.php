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
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU LICENCA.txt *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBAAdCont extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct(){
	parent::Persistente();
    }

    public function recuperaDadosAditivoContrato(&$rsRecordSet, $stCondicao =  '', $stOrdem =  '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montarRecuperaDadosAditivoContrato().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montarRecuperaDadosAditivoContrato()
    {
        $stSql = "
                  SELECT 1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       , contrato_aditivos.num_aditivo
                       , contrato.numero_contrato
                       , contrato_aditivos.objeto
                       , TO_CHAR(contrato_aditivos.dt_assinatura, 'DDMMYYYY') AS dt_assinatura_aditivo
                       , TO_CHAR(contrato_aditivos.dt_vencimento, 'DDMMYYYY') AS dt_vencimento_aditivo
                       , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial
                       , TO_CHAR(publicacao_contrato_aditivos.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao_aditivo
                       , contrato_aditivos.valor_contratado AS valor_contratado_aditivo
                       , TO_CHAR(contrato.dt_assinatura, 'YYYYMM') AS competencia
                       , TO_CHAR(contrato.inicio_execucao, 'DDMMYYYY') AS inicio_execucao_contrato
                       , contrato_aditivos.fundamentacao
                       , 'N' AS exame_previo
                    
                    FROM licitacao.contrato
                    
              INNER JOIN licitacao.contrato_aditivos
                      ON contrato_aditivos.exercicio_contrato = contrato.exercicio
                     AND contrato_aditivos.cod_entidade       = contrato.cod_entidade
                     AND contrato_aditivos.num_contrato       = contrato.num_contrato
                    
              INNER JOIN licitacao.publicacao_contrato_aditivos
                      ON contrato_aditivos.exercicio_contrato = publicacao_contrato_aditivos.exercicio_contrato
                     AND contrato_aditivos.cod_entidade       = publicacao_contrato_aditivos.cod_entidade
                     AND contrato_aditivos.num_contrato       = publicacao_contrato_aditivos.num_contrato
                     AND contrato_aditivos.exercicio          = publicacao_contrato_aditivos.exercicio
                     AND contrato_aditivos.num_aditivo        = publicacao_contrato_aditivos.num_aditivo
                    
              INNER JOIN licitacao.publicacao_contrato
                      ON contrato.num_contrato = publicacao_contrato.num_contrato
                     AND contrato.exercicio    = publicacao_contrato.exercicio
                     AND contrato.cod_entidade = publicacao_contrato.cod_entidade
                    
              INNER JOIN sw_cgm AS cgm_imprensa
                      ON publicacao_contrato.numcgm = cgm_imprensa.numcgm
                    
	             LEFT JOIN licitacao.contrato_compra_direta
	                    ON contrato_compra_direta.num_contrato  = contrato.num_contrato
		                 AND contrato_compra_direta.cod_entidade  = contrato.cod_entidade  
		                 AND contrato_compra_direta.exercicio     = contrato.exercicio
		     
	             LEFT JOIN compras.compra_direta
	                    ON compra_direta.cod_compra_direta  = contrato_compra_direta.cod_compra_direta
		                 AND compra_direta.cod_entidade       = contrato_compra_direta.cod_entidade
		                 AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio_compra_direta
		                 AND compra_direta.cod_modalidade     = contrato_compra_direta.cod_modalidade
                    
                   WHERE contrato_aditivos.exercicio = '".$this->getDado('exercicio')."'
                     AND contrato_aditivos.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY')
                                                             AND TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY')
                     AND contrato_aditivos.cod_entidade IN (".$this->getDado('entidades').")
        ";
        return $stSql;
    }

}

?>