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
    * Data de Criação: 12/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63825 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBACertidoesContratos extends Persistente
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
    
    $stSql = " SELECT 1 AS tipo_registro
                    , ".$this->getDado('inCodGestora')." AS unidade_gestora
                    , contrato_documento.exercicio        
                    , documento_de_para.cod_tipo_tcm   
                    , contrato.numero_contrato   
                    , contrato_documento.num_documento  
                    , TO_CHAR(contrato_documento.dt_emissao ,'DDMMYYYY') AS dt_emissao 
                    , TO_CHAR(contrato_documento.dt_validade,'DDMMYYYY') AS dt_validade
                    , '".$this->getDado('competencia')."' AS competencia
                    , documento.nom_documento AS descricao

                 FROM licitacao.documento

           INNER JOIN licitacao.licitacao_documentos
                   ON licitacao_documentos.cod_documento = documento.cod_documento

           INNER JOIN licitacao.participante_documentos
                   ON participante_documentos.cod_documento = licitacao_documentos.cod_documento
                  AND participante_documentos.cod_licitacao = licitacao_documentos.cod_licitacao
                  AND participante_documentos.cod_modalidade = licitacao_documentos.cod_modalidade
                  AND participante_documentos.cod_entidade = licitacao_documentos.cod_entidade
                  AND participante_documentos.exercicio = licitacao_documentos.exercicio

           INNER JOIN licitacao.contrato_documento
                   ON documento.cod_documento  = contrato_documento.cod_documento

           INNER JOIN licitacao.contrato
                   ON contrato.exercicio = contrato_documento.exercicio
                  AND contrato.cod_entidade = contrato_documento.cod_entidade
                  AND contrato.num_contrato = contrato_documento.num_contrato

            LEFT JOIN ( SELECT documento_de_para.cod_documento_tcm AS cod_tipo_tcm
                             , documento_de_para.cod_documento
                          FROM tcmba.documento_de_para 
                    INNER JOIN licitacao.documento
                            ON documento.cod_documento = documento_de_para.cod_documento
                      ) AS documento_de_para
                   ON documento_de_para.cod_documento = contrato_documento.cod_documento

              WHERE contrato.vencimento >= TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy')
                AND contrato_documento.dt_validade >= TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy')
                AND contrato_documento.cod_entidade IN (".$this->getDado('stEntidades').") 
 
           GROUP BY contrato_documento.exercicio
                    , documento_de_para.cod_tipo_tcm  
                    , contrato.numero_contrato  
                    , contrato_documento.num_documento 
                    , contrato_documento.dt_emissao    
                    , contrato_documento.dt_validade
                    , documento.nom_documento
                      
           ORDER BY contrato_documento.exercicio     
                    , contrato_documento.dt_validade
                    , contrato_documento.dt_emissao    
                    , contrato.numero_contrato
                    , contrato_documento.num_documento
                    , documento_de_para.cod_tipo_tcm
                    , documento.nom_documento
    ";
    
    return $stSql;
}

}

?>