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
    * Extensão da Classe de mapeamento
    * Data de Criação: 05/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63407 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBACertidoes extends Persistente
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
    $stSql .= " SELECT 1 AS tipo_registro
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR ,4,'0') AS processo_licitatorio
                     , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf         
                            WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj         
                            ELSE ''         
                     END AS cpf_cnpj
                    , documento_de_para.cod_documento_tcm AS cod_tipo
                    , participante_documentos.num_documento AS num_certidao        
                    , TO_CHAR(participante_documentos.dt_emissao ,'dd/mm/yyyy') AS dt_emissao         
                    , TO_CHAR(participante_documentos.dt_validade,'dd/mm/yyyy') AS dt_validade
                    , ".$this->getDado('exercicio')."::VARCHAR||".$this->getDado('mes')."::VARCHAR AS competencia

                FROM licitacao.documento

          INNER JOIN licitacao.licitacao_documentos
                  ON licitacao_documentos.cod_documento = documento.cod_documento

          INNER JOIN licitacao.licitacao
                  ON licitacao.cod_licitacao  = licitacao_documentos.cod_licitacao
                 AND licitacao.cod_modalidade = licitacao_documentos.cod_modalidade
                 AND licitacao.cod_entidade   = licitacao_documentos.cod_entidade
                 AND licitacao.exercicio      = licitacao_documentos.exercicio

          INNER JOIN licitacao.participante_documentos
                  ON participante_documentos.cod_documento  = licitacao_documentos.cod_documento
                 AND participante_documentos.cod_licitacao  = licitacao_documentos.cod_licitacao
                 AND participante_documentos.cod_modalidade = licitacao_documentos.cod_modalidade
                 AND participante_documentos.cod_entidade   = licitacao_documentos.cod_entidade
                 AND participante_documentos.exercicio      = licitacao_documentos.exercicio

           LEFT JOIN sw_cgm_pessoa_fisica
                  ON participante_documentos.cgm_fornecedor = sw_cgm_pessoa_fisica.numcgm

           LEFT JOIN sw_cgm_pessoa_juridica
                  ON participante_documentos.cgm_fornecedor = sw_cgm_pessoa_juridica.numcgm

           LEFT JOIN tcmba.documento_de_para
                  ON documento_de_para.cod_documento = participante_documentos.cod_documento
                  
               WHERE licitacao_documentos.exercicio  = '".$this->getDado('exercicio')."'
                 AND licitacao.cod_modalidade NOT IN (8, 9)
                 AND licitacao_documentos.cod_entidade IN (".$this->getDado('entidades').")
                 AND TO_DATE(TO_CHAR(licitacao.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

     GROUP BY licitacao_documentos.exercicio
            , participante_documentos.cod_licitacao         
            , sw_cgm_pessoa_fisica.cpf         
            , sw_cgm_pessoa_juridica.cnpj         
            , documento_de_para.cod_documento_tcm
            , participante_documentos.num_documento         
            , participante_documentos.dt_emissao         
            , participante_documentos.dt_validade
            , processo_licitatorio

     ORDER BY participante_documentos.cod_licitacao         
            , sw_cgm_pessoa_fisica.cpf         
            , sw_cgm_pessoa_juridica.cnpj         
            , documento_de_para.cod_documento_tcm
            , participante_documentos.num_documento         
            , participante_documentos.dt_emissao         
            , participante_documentos.dt_validade         
";
    return $stSql;
}

}

?>