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
    * Classe de mapeamento da tabela licitacao.participante
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoParticipante.class.php 66191 2016-07-28 14:03:35Z carlos.silva $

    * Casos de uso: uc-03.05.18
            uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Thiago La Delfa Cabelleira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoParticipante extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoParticipante()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licitacao,cgm_fornecedor,cod_modalidade,cod_entidade,exercicio');

    $this->AddCampo('cod_licitacao'        ,'integer' ,false ,''  ,true   ,'TLicitacaoLicitacao'             );
    $this->AddCampo('cgm_fornecedor'       ,'integer' ,false ,''  ,true   ,'TComprasFornecedor'              );
    $this->AddCampo('cod_modalidade'       ,'integer' ,false ,''  ,true   ,'TLicitacaoLicitacao'             );
    $this->AddCampo('cod_entidade'         ,'integer' ,false ,''  ,true   ,'TLicitacaoLicitacao'             );
    $this->AddCampo('exercicio'            ,'char'    ,false ,'4' ,true   ,'TLicitacaoLicitacao' ,'exercicio');
    $this->AddCampo('numcgm_representante' ,'integer' ,false ,''  ,false  ,'TCGMCGM'             ,'numcgm'   );
    $this->AddCampo('dt_inclusao'          ,'date'    ,false ,''  ,true   ,false                 ,false      );
    $this->AddCampo('renuncia_recurso'     ,'boolean' ,false ,''  ,false  ,false                 ,false      );
}

function recuperaParticipanteLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaParticipanteLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaParticipanteLicitacao()
{
    $stSql = "
            SELECT
                    ll.cod_entidade
                 ,  entidade.nom_cgm AS nom_entidade
                 ,  ll.cod_modalidade
                 ,  modalidade.descricao AS nom_modalidade
                 ,  ll.exercicio
                 ,  ll.cod_licitacao
                 ,  ll.cod_objeto
                 ,  cgm.numcgm
                 ,  cgm.nom_cgm
                 ,  lp.cgm_fornecedor
                 ,  lp.numcgm_representante
                 
              FROM  licitacao.licitacao AS ll
         
         LEFT JOIN  licitacao.edital AS le
                ON  ll.cod_licitacao = le.cod_licitacao
               AND  ll.cod_modalidade = le.cod_modalidade
               AND  ll.cod_entidade = le.cod_entidade
               AND  ll.exercicio = le.exercicio
        
        INNER JOIN  licitacao.participante AS lp
                ON  lp.cod_licitacao = ll.cod_licitacao
               AND  lp.cod_modalidade = ll.cod_modalidade
               AND  lp.cod_entidade = ll.cod_entidade
               AND  lp.exercicio = ll.exercicio
        
        INNER JOIN  sw_cgm AS cgm
                ON  cgm.numcgm = lp.cgm_fornecedor
        
        INNER JOIN  compras.modalidade
                ON  modalidade.cod_modalidade = ll.cod_modalidade
        
        INNER JOIN  orcamento.entidade AS oe
                ON  oe.exercicio = ll.exercicio
               AND  oe.cod_entidade = ll.cod_entidade
        
        INNER JOIN  sw_cgm AS entidade
                ON  entidade.numcgm = oe.numcgm
             
             WHERE  NOT EXISTS (  SELECT  1
                                    FROM  licitacao.edital_anulado
                                   WHERE  edital_anulado.num_edital = le.num_edital
                                     AND  edital_anulado.exercicio = le.exercicio
                               )
                               
               -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
               AND CASE WHEN ll.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                    
                   le.cod_licitacao  IS NOT NULL
               AND le.cod_modalidade IS NOT NULL
               AND le.cod_entidade   IS NOT NULL 
               AND le.exercicio      IS NOT NULL 

              -- Para as modalidades 8,9 é facultativo possuir um edital
              WHEN ll.cod_modalidade in (8,9) THEN
                    
                    le.cod_licitacao  IS NULL
                 OR le.cod_modalidade IS NULL
                 OR le.cod_entidade   IS NULL 
                 OR le.exercicio      IS NULL 

	         OR le.cod_licitacao  IS NOT NULL
	         OR le.cod_modalidade IS NOT NULL
	         OR le.cod_entidade   IS NOT NULL 
	         OR le.exercicio      IS NOT NULL 
            END  \n
    ";

    if ($this->getDado('cod_licitacao')) {
        $stSql .="  AND ll.cod_licitacao = ".$this->getDado('cod_licitacao')."\n";
    }

    if ($this->getDado('cod_modalidade')) {
        $stSql .="  AND ll.cod_modalidade = ".$this->getDado('cod_modalidade')."\n";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql .=" AND ll.cod_entidade = ".$this->getDado('cod_entidade')."\n";
    }

    if ($this->getDado('num_edital')) {
      $stSql .="    AND ll.num_edital = ".$this->getDado('num_edital')."  \n";
    }
    if ($this->getDado('exercicio')) {
      $stSql .="    AND ll.exercicio = '".$this->getDado('exercicio')."'  \n";
    }
    $stSql .="    ORDER BY cgm.numcgm \n";

    return $stSql;
}

function recuperaParticipanteLicitacaoHabilitacaoLista(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaParticipanteLicitacaoHabilitacaoLista().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaParticipanteLicitacaoHabilitacaoLista()
{
    $stSql = "
            SELECT licitacao.cod_processo
		 , licitacao.cod_licitacao||'/'||licitacao.exercicio as num_licitacao                 
                 , licitacao.exercicio
		 , licitacao.exercicio_processo
		 , licitacao.cod_modalidade
                 , licitacao.cod_entidade
                 , cgm_entidade.nom_cgm AS nom_entidade
                 , modalidade.descricao 
                 , licitacao.cod_licitacao
                 , edital.num_edital
             
             FROM licitacao.licitacao
                           
        LEFT JOIN licitacao.edital
               ON edital.cod_licitacao   = licitacao.cod_licitacao                                
              AND edital.cod_modalidade  = licitacao.cod_modalidade                               
              AND edital.cod_entidade    = licitacao.cod_entidade                                 
              AND edital.exercicio       = licitacao.exercicio                                    
              
       INNER JOIN licitacao.comissao_licitacao                            
               ON comissao_licitacao.cod_licitacao  = licitacao.cod_licitacao                                 
              AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade                                
              AND comissao_licitacao.cod_entidade   = licitacao.cod_entidade                                  
              AND comissao_licitacao.exercicio      = licitacao.exercicio                                     
       
       INNER JOIN compras.modalidade                                
               ON licitacao.cod_modalidade = modalidade.cod_modalidade                                
       
       INNER JOIN orcamento.entidade
               ON entidade.cod_entidade = licitacao.cod_entidade                                    
              AND entidade.exercicio    = licitacao.exercicio                                       

       INNER JOIN  licitacao.participante
               ON  participante.cod_licitacao  = licitacao.cod_licitacao
              AND  participante.cod_modalidade = licitacao.cod_modalidade
              AND  participante.cod_entidade   = licitacao.cod_entidade
              AND  participante.exercicio      = licitacao.exercicio
              
       INNER JOIN  sw_cgm AS cgm
               ON  cgm.numcgm = participante.cgm_fornecedor

       INNER JOIN  sw_cgm AS cgm_entidade
               ON  cgm_entidade.numcgm = entidade.numcgm
                     
            WHERE NOT EXISTS ( SELECT 1
                                 FROM licitacao.edital_anulado
                                WHERE edital_anulado.num_edital = edital.num_edital
                                  AND edital_anulado.exercicio  = edital.exercicio
                              )
                              
            -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
            AND CASE WHEN licitacao.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                    
                    edital.cod_licitacao IS NOT NULL
               AND edital.cod_modalidade IS NOT NULL
               AND edital.cod_entidade   IS NOT NULL 
               AND edital.exercicio      IS NOT NULL 

           -- Para as modalidades 8,9 é facultativo possuir um edital
              WHEN licitacao.cod_modalidade in (8,9) THEN
                    
                    edital.cod_licitacao  IS NULL
                 OR edital.cod_modalidade IS NULL
                 OR edital.cod_entidade   IS NULL 
                 OR edital.exercicio      IS NULL 

             OR edital.cod_licitacao  IS NOT NULL
             OR edital.cod_modalidade IS NOT NULL
             OR edital.cod_entidade   IS NOT NULL 
             OR edital.exercicio      IS NOT NULL
             
            END \n";

    if ( $this->getDado( 'num_edital' ) ) {
        $stSql .= " AND edital.num_edital = '". $this->getDado( 'num_edital' )."' \n";
    }
    
    if ( $this->getDado( 'exercicio' ) ) {
        $stSql .= " AND licitacao.exercicio = '". $this->getDado( 'exercicio' )."' \n";
    }
    
    if ( $this->getDado( 'cod_entidade' ) ) {
        $stSql .= " AND licitacao.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) \n";
    }

    if ( $this->getDado( 'cod_modalidade' ) ) {
        $stSql .= " AND licitacao.cod_modalidade = ". $this->getDado( 'cod_modalidade' )." \n";
    }

    if ( $this->getDado( 'cod_licitacao' ) ) {
        $stSql .= " AND licitacao.cod_licitacao = ". $this->getDado( 'cod_licitacao' )." \n";
    }

    if ( $this->getDado( 'cod_processo' ) ) {
        $stSql .= " AND licitacao.cod_processo = ". $this->getDado( 'cod_processo' )." \n";
    }

    if ( $this->getDado( 'cod_mapa' ) ) {
        $stSql .= " AND licitacao.cod_mapa = ". $this->getDado( 'cod_mapa' )." \n";
    }

    if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
        $stSql .= " AND licitacao.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' )." \n";
    }

    if ( $this->getDado( 'cod_criterio' ) ) {
        $stSql .= " AND licitacao.cod_criterio = ". $this->getDado( 'cod_criterio' )." \n";
    }

    if ( $this->getDado( 'cod_tipo_objeto' ) ) {
        $stSql .= " AND licitacao.cod_tipo_objeto = ". $this->getDado( 'cod_tipo_objeto' )." \n";
    }

    if ( $this->getDado( 'cod_objeto' ) ) {
        $stSql .= " AND licitacao.cod_objeto = ". $this->getDado( 'cod_objeto' )." \n";
    }

    if ( $this->getDado( 'cod_comissao' ) ) {
        $stSql .= " AND comissao_licitacao.cod_comissao = ". $this->getDado( 'cod_comissao' )." \n";
    }

    if ( $this->getDado( 'cgm_fornecedor' ) ) {
        $stSql .= " AND participante.cgm_fornecedor = ". $this->getDado( 'cgm_fornecedor' )." \n";
    }

    $stSql .="
            GROUP BY licitacao.cod_licitacao
                    , licitacao.cod_modalidade
                    , licitacao.cod_entidade
                    , licitacao.cod_processo
                    , licitacao.exercicio
                    , licitacao.exercicio_processo
                    , cgm_entidade.nom_cgm 
                    , modalidade.descricao 
                    , edital.num_edital
    
             ORDER BY licitacao.cod_processo ";

    return $stSql;
}

function recuperaParticipanteLicitacaoHabilitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaParticipanteLicitacaoHabilitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaParticipanteLicitacaoHabilitacao()
{
    $stSql = "
            SELECT licitacao.cod_processo
		 , licitacao.cod_licitacao||'/'||licitacao.exercicio as num_licitacao                 
		 , licitacao.exercicio_processo
		 , modalidade.descricao 
		 , licitacao.cod_modalidade
                 , licitacao.cod_entidade
                 , cgm_entidade.nom_cgm AS nom_entidade
                 , licitacao.cod_modalidade
                 , modalidade.descricao AS nom_modalidade
                 , licitacao.exercicio
                 , licitacao.cod_licitacao
                 , licitacao.cod_objeto
                 , cgm.numcgm
                 , cgm.nom_cgm
                 , participante.cgm_fornecedor
                 , participante.numcgm_representante
                 , edital.num_edital
             
             FROM licitacao.licitacao
                           
        LEFT JOIN licitacao.edital
               ON edital.cod_licitacao   = licitacao.cod_licitacao                                
              AND edital.cod_modalidade  = licitacao.cod_modalidade                               
              AND edital.cod_entidade    = licitacao.cod_entidade                                 
              AND edital.exercicio       = licitacao.exercicio                                    
              
       INNER JOIN licitacao.comissao_licitacao                            
               ON comissao_licitacao.cod_licitacao  = licitacao.cod_licitacao                                 
              AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade                                
              AND comissao_licitacao.cod_entidade   = licitacao.cod_entidade                                  
              AND comissao_licitacao.exercicio      = licitacao.exercicio                                     
       
       INNER JOIN compras.modalidade                                
               ON licitacao.cod_modalidade = modalidade.cod_modalidade                                
       
       INNER JOIN orcamento.entidade
               ON entidade.cod_entidade = licitacao.cod_entidade                                    
              AND entidade.exercicio    = licitacao.exercicio                                       

       INNER JOIN  licitacao.participante
               ON  participante.cod_licitacao  = licitacao.cod_licitacao
              AND  participante.cod_modalidade = licitacao.cod_modalidade
              AND  participante.cod_entidade   = licitacao.cod_entidade
              AND  participante.exercicio      = licitacao.exercicio
              
       INNER JOIN  sw_cgm AS cgm
               ON  cgm.numcgm = participante.cgm_fornecedor

       INNER JOIN  sw_cgm AS cgm_entidade
               ON  cgm_entidade.numcgm = entidade.numcgm
                     
            WHERE NOT EXISTS ( SELECT 1
                                 FROM licitacao.edital_anulado
                                WHERE edital_anulado.num_edital = edital.num_edital
                                  AND edital_anulado.exercicio  = edital.exercicio
                              )
            -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
            AND CASE WHEN licitacao.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                    
                    edital.cod_licitacao IS NOT NULL
               AND edital.cod_modalidade IS NOT NULL
               AND edital.cod_entidade   IS NOT NULL 
               AND edital.exercicio      IS NOT NULL 

           -- Para as modalidades 8,9 é facultativo possuir um edital
              WHEN licitacao.cod_modalidade in (8,9) THEN
                    
                    edital.cod_licitacao  IS NULL
                 OR edital.cod_modalidade IS NULL
                 OR edital.cod_entidade   IS NULL 
                 OR edital.exercicio      IS NULL 

             OR edital.cod_licitacao  IS NOT NULL
             OR edital.cod_modalidade IS NOT NULL
             OR edital.cod_entidade   IS NOT NULL 
             OR edital.exercicio      IS NOT NULL
             
            END \n";

    if ( $this->getDado( 'num_edital' ) ) {
        $stSql .= " AND edital.num_edital = '". $this->getDado( 'num_edital' )."' \n";
    }
    
    if ( $this->getDado( 'exercicio' ) ) {
        $stSql .= " AND licitacao.exercicio = '". $this->getDado( 'exercicio' )."' \n";
    }
    
    if ( $this->getDado( 'cod_entidade' ) ) {
        $stSql .= " AND licitacao.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) \n";
    }

    if ( $this->getDado( 'cod_modalidade' ) ) {
        $stSql .= " AND licitacao.cod_modalidade = ". $this->getDado( 'cod_modalidade' )." \n";
    }

    if ( $this->getDado( 'cod_licitacao' ) ) {
        $stSql .= " AND licitacao.cod_licitacao = ". $this->getDado( 'cod_licitacao' )." \n";
    }

    if ( $this->getDado( 'cod_processo' ) ) {
        $stSql .= " AND licitacao.cod_processo = ". $this->getDado( 'cod_processo' )." \n";
    }

    if ( $this->getDado( 'cod_mapa' ) ) {
        $stSql .= " AND licitacao.cod_mapa = ". $this->getDado( 'cod_mapa' )." \n";
    }

    if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
        $stSql .= " AND licitacao.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' )." \n";
    }

    if ( $this->getDado( 'cod_criterio' ) ) {
        $stSql .= " AND licitacao.cod_criterio = ". $this->getDado( 'cod_criterio' )." \n";
    }

    if ( $this->getDado( 'cod_tipo_objeto' ) ) {
        $stSql .= " AND licitacao.cod_tipo_objeto = ". $this->getDado( 'cod_tipo_objeto' )." \n";
    }

    if ( $this->getDado( 'cod_objeto' ) ) {
        $stSql .= " AND licitacao.cod_objeto = ". $this->getDado( 'cod_objeto' )." \n";
    }

    if ( $this->getDado( 'cod_comissao' ) ) {
        $stSql .= " AND comissao_licitacao.cod_comissao = ". $this->getDado( 'cod_comissao' )." \n";
    }

    $stSql .=" ORDER BY cgm.numcgm ";

    return $stSql;
}

/**
 * Retorna os participantes
 */
function recuperaParticipantes(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", "" );
}

function montaRecuperaRelacionamento()
{
    $stSql = " select participante.cod_licitacao
                    , participante.cgm_fornecedor
                    , participante.cod_modalidade
                    , participante.cod_entidade
                    , participante.exercicio
                    , participante.numcgm_representante
                    , participante.dt_inclusao
                    , participante.renuncia_recurso
                    , sw_cgm_fornecedor.nom_cgm as fornecedor
                    , sw_cgm_representante.nom_cgm as representante
                    , participante_consorcio.numcgm as cgm_consorcio
                    , sw_cgm_consorcio.nom_cgm as consorcio
                 from licitacao.participante
           inner join licitacao.licitacao
                   on licitacao.cod_licitacao = participante.cod_licitacao
                  and licitacao.cod_modalidade = participante.cod_modalidade
                  and licitacao.cod_entidade = participante.cod_entidade
                  and licitacao.exercicio = participante.exercicio
            left join licitacao.participante_consorcio
                   on participante.cod_licitacao  = participante_consorcio.cod_licitacao
                  and participante.cod_modalidade = participante_consorcio.cod_modalidade
                  and participante.cod_entidade   = participante_consorcio.cod_entidade
                  and participante.exercicio      = participante_consorcio.exercicio
                  and participante.cgm_fornecedor = participante_consorcio.cgm_fornecedor
           inner join compras.fornecedor
                   on fornecedor.cgm_fornecedor = participante.cgm_fornecedor
           inner join sw_cgm as sw_cgm_fornecedor
                   on sw_cgm_fornecedor.numcgm = fornecedor.cgm_fornecedor
           inner join sw_cgm as sw_cgm_representante
                   on sw_cgm_representante.numcgm = participante.numcgm_representante
            left join sw_cgm as sw_cgm_consorcio
                   on sw_cgm_consorcio.numcgm = participante_consorcio.numcgm
                where ";
    if ( $this->getDado('cod_licitacao') ) {
        $stFiltro.= " AND participante.cod_licitacao =  ".$this->getDado('cod_licitacao');
    }
    if ( $this->getDado('exercicio') ) {
        $stFiltro.= " AND licitacao.exercicio = '".$this->getDado('exercicio')."' ";
    }
    if ( $this->getDado('cod_entidade') ) {
        $stFiltro.= "   AND licitacao.cod_entidade     = '".$this->getDado('cod_entidade')."'";
    }
    if ( $this->getDado('cod_modalidade') ) {
        $stFiltro.= "   AND licitacao.cod_modalidade   = '".$this->getDado('cod_modalidade')."'";
    }
    if ( $this->getDado('cgm_fornecedor' ) ) {
        $stFiltro.= "   AND participante.cgm_fornecedor   = ".$this->getDado('cgm_fornecedor');
    }

    $stSql .= ( $stFiltro ) ? substr($stFiltro,4) : '';

    return $stSql;

}

function recuperaDocumentoParticipante(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentoParticipante().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentoParticipante()
{
    $stSql  ="SELECT                                         \n";
    $stSql .="     pd.cod_documento                         \n";
    $stSql .="    ,ld.nom_documento                         \n";
    $stSql .="    ,pd.num_documento                         \n";
    $stSql .="    ,to_char(pd.dt_validade,'dd/mm/yyyy')  as dt_validade   \n";
    $stSql .="    ,cgm.numcgm                               \n";
    $stSql .="    ,cgm.nom_cgm                               \n";
    $stSql .="FROM                                          \n";
    $stSql .="    licitacao.documento as ld                 \n";
    $stSql .="   ,sw_cgm as cgm                             \n";
    //$stSql .="   ,licitacao.participante as lp              \n";
    $stSql .="   ,licitacao.edital as le                    \n";
    $stSql .="   ,licitacao.participante_documentos as pd   \n";
    $stSql .="WHERE                                         \n";
    //$stSql .="       pd.cod_licitacao = lp.cod_licitacao    \n";
    $stSql .="       pd.cgm_fornecedor = cgm.numcgm         \n";
    $stSql .="   and pd.cod_documento = ld.cod_documento    \n";
    //$stSql .="   and pd.cod_modalidade = lp.cod_modalidade  \n";
    //$stSql .="   and pd.cod_entidade = lp.cod_entidade      \n";
    //$stSql .="   and pd.exercicio = lp.exercicio            \n";

    if ($this->getDado('cod_licitacao')) {
        $stSql .="  and le.cod_licitacao = ".$this->getDado('cod_licitacao')."\n";
    }

    if ($this->getDado('cod_modalidade')) {
        $stSql .="  and le.cod_modalidade = ".$this->getDado('cod_modalidade')."\n";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql .=" and le.cod_entidade = ".$this->getDado('cod_entidade')."\n";
    }

    if ($this->getDado('num_edital')) {
      $stSql .="    AND le.num_edital = ".$this->getDado('num_edital')."\n";
    }

    if ($this->getDado('numcgm')) {
      $stSql .="    AND cgm.numcgm    = ".$this->getDado('numcgm')."\n";
    }

    if ($this->getDado('exercicio') ) {
        $stSql .= " AND le.exercicio = ". $this->getDado('exercicio')."\n";
    }

    if ($this->getDado('cgm_fornecedor') ) {
        $stSql .= " AND pd.cgm_fornecedor = ". $this->getDado('cgm_fornecedor')."\n";
    }

    return $stSql;

  }//fim da function

    public function recuperaConvidadoLicitacaoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaConvidadoLicitacaoEsfinge().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaConvidadoLicitacaoEsfinge()
    {
        $stSql = "
            select case when sw_cgm_pessoa_fisica.numcgm is not null then '01'
                        when sw_cgm_pessoa_juridica.numcgm is not null then '02'
                        else '00'
                   end as tipo_pessoa
                 , case when sw_cgm_pessoa_fisica.numcgm is not null then sw_cgm_pessoa_fisica.cpf
                        when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj
                        else ''
                   end as cic_do_participante
                 , licitacao.cod_licitacao
                 , sw_cgm.nom_cgm
                 , to_char( participante.dt_inclusao, 'dd/mm/yyyy' ) as dt_inclusao
            from licitacao.participante

            join sw_cgm
            on sw_cgm.numcgm = participante.cgm_fornecedor

            left join sw_cgm_pessoa_fisica
            on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

            left join sw_cgm_pessoa_juridica
            on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

            join licitacao.licitacao
            on licitacao.cod_licitacao = participante.cod_licitacao
            and licitacao.cod_modalidade = participante.cod_modalidade
            and licitacao.cod_entidade = participante.cod_entidade
            and licitacao.exercicio = participante.exercicio
            and licitacao.cod_modalidade = 1

            where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
            and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
            and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
            and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )

        ";

        return $stSql;

    }

    public function recuperaParticipanteLicitacaoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaParticipanteLicitacaoEsfinge().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaParticipanteLicitacaoEsfinge()
    {
        $stSql = "

select licitacao.cod_licitacao
     , case when sw_cgm_pessoa_fisica.numcgm is not null then '01'
            when sw_cgm_pessoa_juridica.numcgm is not null then '02'
            else '00'
       end as tipo_pessoa
     , case when sw_cgm_pessoa_fisica.numcgm is not null then sw_cgm_pessoa_fisica.cpf
            when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj
            else ''
       end as cic_participante
     , case when participante_consorcio.cgm_fornecedor is not null then '02'
            else '01'
       end as tipo_participacao
     , sw_cgm.nom_cgm
     , to_char( validade_cotacao_fornecedor.dt_validade, 'dd/mm/yyyy' ) as dt_validade
from licitacao.licitacao

join licitacao.participante
on participante.cod_licitacao = licitacao.cod_licitacao
and participante.cod_modalidade = licitacao.cod_modalidade
and participante.cod_entidade = licitacao.cod_entidade
and participante.exercicio = licitacao.exercicio

join sw_cgm
on sw_cgm.numcgm = participante.cgm_fornecedor

left join sw_cgm_pessoa_fisica
on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

left join sw_cgm_pessoa_juridica
on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

left join licitacao.participante_consorcio
on participante_consorcio.cod_licitacao = participante.cod_licitacao
and participante_consorcio.cgm_fornecedor = participante.cgm_fornecedor
and participante_consorcio.cod_modalidade = participante.cod_modalidade
and participante_consorcio.cod_entidade = participante.cod_entidade
and participante_consorcio.exercicio = participante.exercicio

join compras.mapa_cotacao
on mapa_cotacao.exercicio_mapa = licitacao.exercicio_mapa
and mapa_cotacao.cod_mapa = licitacao.cod_mapa

join compras.cotacao
on cotacao.exercicio = mapa_cotacao.exercicio_cotacao
and cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

join (
        select exercicio
             , cod_cotacao
             , lote
             , cgm_fornecedor
             , max(dt_validade) as dt_validade
          from compras.cotacao_fornecedor_item
      group by exercicio
             , cod_cotacao
             , cgm_fornecedor
             , lote
     ) as validade_cotacao_fornecedor
on validade_cotacao_fornecedor.exercicio = cotacao.exercicio
and validade_cotacao_fornecedor.cod_cotacao = cotacao.cod_cotacao
and validade_cotacao_fornecedor.cgm_fornecedor = participante.cgm_fornecedor

where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )

        ";

        return $stSql;
    }

    public function recuperaCadastroParticipanteLicitacaoEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaCadastroParticipanteLicitacaoEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaCadastroParticipanteLicitacaoEsfinge()
    {
        $stSql = "
                select licitacao.cod_licitacao
                     , case when sw_cgm_pessoa_fisica.numcgm is not null then '01'
                            when sw_cgm_pessoa_juridica.numcgm is not null then '02'
                            else '00'
                       end as tipo_pessoa
                     , case when sw_cgm_pessoa_fisica.numcgm is not null then sw_cgm_pessoa_fisica.cpf
                            when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj
                            else ''
                       end as cic_participante
                     , cgm_entidade.nom_cgm
                     , participante_certificacao.num_certificacao
                     , to_char(participante_certificacao.dt_registro,'dd/mm/yyyy') as dt_registro
                     , to_char(participante_certificacao.final_vigencia,'dd/mm/yyyy') as final_vigencia

                from licitacao.licitacao

                join orcamento.entidade
                on entidade.cod_entidade = licitacao.cod_entidade
                and entidade.exercicio = licitacao.exercicio

                join sw_cgm as cgm_entidade
                on cgm_entidade.numcgm = entidade.numcgm

                join licitacao.participante
                on participante.cod_licitacao = licitacao.cod_licitacao
                and participante.cod_modalidade = licitacao.cod_modalidade
                and participante.cod_entidade = licitacao.cod_entidade
                and participante.exercicio = licitacao.exercicio

                join sw_cgm
                on sw_cgm.numcgm = participante.cgm_fornecedor

                left join sw_cgm_pessoa_fisica
                on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                left join sw_cgm_pessoa_juridica
                on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                join licitacao.participante_documentos
                on participante_documentos.cod_licitacao = participante.cod_licitacao
                and participante_documentos.cgm_fornecedor = participante.cgm_fornecedor
                and participante_documentos.cod_modalidade = participante.cod_modalidade
                and participante_documentos.cod_entidade = participante.cod_entidade
                and participante_documentos.exercicio = participante.exercicio

                join licitacao.participante_certificacao
                on participante_certificacao.exercicio = participante.exercicio
                and participante_certificacao.cgm_fornecedor = participante.cgm_fornecedor

                join licitacao.certificacao_documentos
                on certificacao_documentos.num_certificacao = participante_certificacao.num_certificacao
                and certificacao_documentos.exercicio       = participante_certificacao.exercicio
                and certificacao_documentos.cgm_fornecedor  = participante_certificacao.cgm_fornecedor

                where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
                and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
                and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
        ";

        return $stSql;

    }

    public function recuperaParticipanteLicitacaoManutencaoPropostas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaParticipanteLicitacaoManutencaoPropostas().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaParticipanteLicitacaoManutencaoPropostas()
    {
    $stSql = "
        SELECT ll.cod_entidade
             ,  entidade.nom_cgm AS nom_entidade
             ,  ll.cod_modalidade
             ,  modalidade.descricao AS nom_modalidade
             ,  ll.exercicio
             ,  le.cod_licitacao
             ,  ll.cod_objeto
             --,  cgm.numcgm
             --,  cgm.nom_cgm
             ,  lp.cgm_fornecedor
             ,  cgm_fornecedor.nom_cgm as fornecedor
             ,  lp.numcgm_representante
             ,  cgm_representante.nom_cgm as representante
             ,  cgm_consorcio.nom_cgm as consorc
             ,  lp.dt_inclusao
             
          FROM  licitacao.licitacao AS ll
     
     LEFT JOIN  licitacao.edital AS le
            ON  ll.cod_licitacao = le.cod_licitacao
           AND  ll.cod_modalidade = le.cod_modalidade
           AND  ll.cod_entidade = le.cod_entidade
           AND  ll.exercicio = le.exercicio
    
    INNER JOIN  licitacao.participante AS lp
            ON  lp.cod_licitacao = ll.cod_licitacao
           AND  lp.cod_modalidade = ll.cod_modalidade
           AND  lp.cod_entidade = ll.cod_entidade
           AND  lp.exercicio = ll.exercicio
           
    INNER JOIN  sw_cgm AS cgm_fornecedor
            ON  cgm_fornecedor.numcgm = lp.cgm_fornecedor
    
    INNER JOIN  sw_cgm AS cgm_representante
            ON  cgm_representante.numcgm = lp.numcgm_representante
     
     LEFT JOIN  licitacao.participante_consorcio lpc
            ON  lp.cod_licitacao  = lpc.cod_licitacao
           AND  lp.cod_modalidade = lpc.cod_modalidade
           AND  lp.cod_entidade   = lpc.cod_entidade
           AND  lp.exercicio      = lpc.exercicio
           AND  lp.cgm_fornecedor = lpc.cgm_fornecedor
           
     LEFT JOIN  sw_cgm AS cgm_consorcio
            ON  cgm_consorcio.numcgm = lpc.numcgm
            
    INNER JOIN  compras.modalidade
            ON  modalidade.cod_modalidade = ll.cod_modalidade
            
    INNER JOIN  orcamento.entidade AS oe
            ON  oe.exercicio = ll.exercicio
           AND  oe.cod_entidade = ll.cod_entidade
           
    INNER JOIN  sw_cgm AS entidade
            ON  entidade.numcgm = oe.numcgm
            
         WHERE  NOT EXISTS (  SELECT  1
                    FROM  licitacao.edital_anulado
                       WHERE  edital_anulado.num_edital = le.num_edital
                     AND  edital_anulado.exercicio = le.exercicio
                   )
                   
           --a quantidade de documentos deve ser a mesma da quantidade de documentos preenchidos para o participante
           AND  ((  SELECT  count(1)
                FROM  licitacao.licitacao_documentos
               WHERE  licitacao_documentos.cod_licitacao = lp.cod_licitacao
                 AND  licitacao_documentos.cod_modalidade = lp.cod_modalidade
                 AND  licitacao_documentos.cod_entidade = lp.cod_entidade
                 AND  licitacao_documentos.exercicio = lp.exercicio
               ) = (
              SELECT  count(1)
                FROM  licitacao.participante_documentos
               WHERE  participante_documentos.cod_licitacao = lp.cod_licitacao
                 AND  participante_documentos.cod_modalidade = lp.cod_modalidade
                 AND  participante_documentos.cgm_fornecedor = lp.cgm_fornecedor
                 AND  participante_documentos.cod_entidade = lp.cod_entidade
                 AND  participante_documentos.exercicio = lp.exercicio
               ) OR lp.cod_modalidade IN (6,7))
    ";
    if ($this->getDado('cod_licitacao')) {
        $stSql .="  and ll.cod_licitacao = ".$this->getDado('cod_licitacao')."\n";
    }

    if ($this->getDado('cod_modalidade')) {
        $stSql .="  and ll.cod_modalidade = ".$this->getDado('cod_modalidade')."\n";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql .=" and ll.cod_entidade = ".$this->getDado('cod_entidade')."\n";
    }

    if ($this->getDado('num_edital')) {
      $stSql .="    AND le.num_edital = ".$this->getDado('num_edital')."      \n";
    }
    if ($this->getDado('exercicio')) {
      $stSql .="    AND ll.exercicio = '".$this->getDado('exercicio')."'        \n";
    }
    $stSql .="    order by cgm_fornecedor.numcgm                                         \n";

    return $stSql;
    }

}//fim da classe
