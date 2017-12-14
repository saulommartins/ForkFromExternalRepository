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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAResConc extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() {
        parent::Persistente();
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
            $stSql = "
                    
                SELECT * FROM (    
                    SELECT '1'                                   AS tipo_registro
                            , edital.cod_edital                     AS numero_concurso
                            , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                            , (SELECT seq
                               FROM (SELECT row_number() over(partition by cod_edital) AS seq, cod_cargo 
                                       FROM concurso".$this->getDado('entidade_rh').".concurso_cargo as cc WHERE cc.cod_edital = edital.cod_edital
                                    ) AS gg
                               WHERE gg.cod_cargo = concurso_cargo.cod_cargo
                            ) AS sequencial_area
                            , concurso_cargo.cod_cargo        AS codigo_cargo_emprego
                            , candidato.cod_candidato         AS inscricao_concurso
                            , sw_cgm_pessoa_fisica.cpf        AS cpf_classificado
                            , remove_acentos(sw_cgm.nom_cgm)  AS nome_concursado
                            , row_number() over (ORDER BY media.valor DESC ) AS numero_classificacao_aprovacao
                            , '".$this->getDado('ano_mes')."' AS competencia
                            , CASE WHEN media.valor >= edital.nota_minima THEN 'aprovado'                                      
			       WHEN media.valor < edital.nota_minima  AND media.valor is not null THEN 'reprovado'             
			       WHEN media.valor is null  THEN 'sem nota'
			      END as situacao
        
                        FROM concurso".$this->getDado('entidade_rh').".edital
    
                        INNER JOIN concurso".$this->getDado('entidade_rh').".concurso_candidato
                                ON concurso_candidato.cod_edital = edital.cod_edital
    
                        INNER JOIN concurso".$this->getDado('entidade_rh').".candidato 
                                ON candidato.cod_candidato = concurso_candidato.cod_candidato
    
                        INNER JOIN concurso".$this->getDado('entidade_rh').".concurso_cargo
                                ON concurso_cargo.cod_edital    = concurso_candidato.cod_edital
                               AND concurso_cargo.cod_cargo    = concurso_candidato.cod_cargo
                        
                        INNER JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = candidato.numcgm
    
                        INNER JOIN sw_cgm
                                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                        
                        INNER JOIN (
                                SELECT candidato.cod_candidato as cod_candidato
                                     , CASE edital.avalia_titulacao                                                             
                                        WHEN 't' 
                                          THEN round((candidato.nota_titulacao + candidato.nota_prova)/2,2)                  
                                        WHEN 'f' 
                                         THEN candidato.nota_prova                                                    
                                      END AS valor
                    
                                 FROM concurso.edital
                    
                           INNER JOIN concurso.concurso_candidato
                                   ON edital.cod_edital = concurso_candidato.cod_edital
                               
                           INNER JOIN concurso.candidato                                                        
                                   ON concurso_candidato.cod_candidato = candidato.cod_candidato
      
                          ) AS media
                          ON media.cod_candidato = candidato.cod_candidato
                          
                ) AS tabela
                
            WHERE tabela.situacao <> 'reprovado' ";
        
        return $stSql;
    }
    
}

?>