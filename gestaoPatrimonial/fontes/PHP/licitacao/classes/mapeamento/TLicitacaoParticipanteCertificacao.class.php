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
    * Classe de mapeamento da tabela licitacao.participante_certificacao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18419 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-11-30 17:37:04 -0200 (Qui, 30 Nov 2006) $

    * Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoParticipanteCertificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante_certificacao");

    $this->setCampoCod('num_certificacao');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('num_certificacao'  ,'sequence',true  ,''   ,true,false);
    $this->AddCampo('exercicio'         ,'char'    ,false ,'4'  ,true,false);
    $this->AddCampo('cgm_fornecedor'    ,'integer' ,false ,''   ,false,'TComprasFornecedor');
    $this->AddCampo('cod_tipo_documento','integer' ,true  ,''   ,false,'TAdministracaoModeloDocumento');
    $this->AddCampo('cod_documento'     ,'integer' ,true  ,''   ,false,'TAdministracaoModeloDocumento');
    $this->AddCampo('dt_registro'       ,'date'    ,true  ,''   ,false,false);
    $this->AddCampo('final_vigencia'    ,'date'    ,false ,''   ,false,false);
    $this->AddCampo('observacao'        ,'text'    ,false ,''   ,false,false);

}

public function montaRecuperaRelacionamento() {
    $stSql = " select * from licitacao.participante_certificacao ";

    return $stSql;
}

public function recuperaListaCertificacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaCertificacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaListaCertificacao()
{
    $stSql = "
            SELECT participante_certificacao.num_certificacao                         
                 , participante_certificacao.exercicio                                
                 , participante_certificacao.cgm_fornecedor                           
                 , TO_CHAR(participante_certificacao.dt_registro, 'dd/mm/yyyy') AS dt_registro    
                 , TO_CHAR(participante_certificacao.final_vigencia, 'dd/mm/yyyy') AS final_vigencia 
                 , participante_certificacao.observacao                               
                 , cgm.nom_cgm
                 , participante_certificacao_licitacao.cod_licitacao                           
                 , participante_certificacao_licitacao.cod_modalidade
                 , participante_certificacao_licitacao.cod_entidade
                 , participante_certificacao_licitacao.cod_entidade || ' - ' || nom_entidade.nom_cgm AS nome_entidade
                 , participante_certificacao_licitacao.exercicio_licitacao
                 
             FROM licitacao.participante_certificacao
                   
       INNER JOIN sw_cgm as cgm
               ON participante_certificacao.cgm_fornecedor = cgm.numcgm
    
        LEFT JOIN licitacao.participante_certificacao_licitacao
               ON participante_certificacao_licitacao.num_certificacao       = participante_certificacao.num_certificacao
              AND participante_certificacao_licitacao.exercicio_certificacao = participante_certificacao.exercicio
              AND participante_certificacao_licitacao.cgm_fornecedor         = participante_certificacao.cgm_fornecedor
        
        LEFT JOIN (
                    SELECT entidade.cod_entidade
                         , entidade.exercicio
			 , nome_cgm.nom_cgm                            

		      FROM orcamento.entidade

		INNER JOIN sw_cgm AS nome_cgm
		        ON entidade.numcgm = nome_cgm.numcgm
                        
                ) AS nom_entidade
               ON nom_entidade.cod_entidade = participante_certificacao_licitacao.cod_entidade
              AND nom_entidade.exercicio    = participante_certificacao_licitacao.exercicio_licitacao
              
            WHERE 1 = 1 ";

    if ( $this->getDado( 'exercicio_licitacao' ) ) {
        $stSql .= " AND participante_certificacao_licitacao.exercicio_licitacao = '".$this->getDado( 'exercicio_licitacao' )."' \n";
    }
    
    if ( $this->getDado( 'cod_entidade' ) ) {
        $stSql .= " AND participante_certificacao_licitacao.cod_entidade = ".$this->getDado( 'cod_entidade' )." \n";
    }
    
    if ( $this->getDado( 'cod_modalidade' ) ) {
        $stSql .= " AND participante_certificacao_licitacao.cod_modalidade = ".$this->getDado( 'cod_modalidade' )." \n";
    }
    
    if ( $this->getDado( 'cod_licitacao' ) ) {
        $stSql .= " AND participante_certificacao_licitacao.cod_licitacao = ".$this->getDado( 'cod_licitacao' )." \n";
    }

    if ( $this->getDado( 'num_certificacao' ) ) {
        $stSql .= " AND participante_certificacao.num_certificacao = ".$this->getDado( 'num_certificacao' )." \n";
    }

    if ( $this->getDado( 'cgm_fornecedor' ) ) {
        $stSql .= " AND participante_certificacao.cgm_fornecedor = ".$this->getDado( 'cgm_fornecedor' )." \n";
    }

    return $stSql;
}

}

?>