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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 31/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/
class TTCEAMCertidao extends Persistente
{
    /**
        * M�todo Construtor
        * @access Private
    */
    public function TTCEAMCertidao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT 0 AS reservado_tc
                 , CASE WHEN licitacao.cod_modalidade = 1 THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 2 THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 3 THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 4 THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 5 THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 6 THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 7 THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 8 THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 9 THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                   END AS processo_licitatorio
                 , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                            sw_cgm_pessoa_fisica.cpf
                        ELSE
                            sw_cgm_pessoa_juridica.cnpj
                   END AS cpf_cnpj
                 , tceam.fn_depara_tipo_certidao(participante_documentos.cod_documento) AS tipo_certidao
                 , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                            1
                        ELSE
                            2
                   END AS tipo_pessoa
                 , CASE WHEN documento.cod_documento = 4 THEN substr(participante_documentos.num_documento,5)
                        ELSE participante_documentos.num_documento
                   END AS num_documento
                 , TO_CHAR(participante_documentos.dt_emissao, 'dd/mm/yyyy') AS dt_emissao
                 , TO_CHAR(participante_documentos.dt_validade, 'dd/mm/yyyy') AS dt_validade
              FROM licitacao.participante_documentos
              JOIN licitacao.licitacao
                ON licitacao.cod_licitacao  = participante_documentos.cod_licitacao
               AND licitacao.cod_modalidade = participante_documentos.cod_modalidade
               AND licitacao.cod_entidade   = participante_documentos.cod_entidade
               AND licitacao.exercicio      = participante_documentos.exercicio
              JOIN sw_cgm
                ON sw_cgm.numcgm = participante_documentos.cgm_fornecedor
         LEFT JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = participante_documentos.cgm_fornecedor
         LEFT JOIN sw_cgm_pessoa_juridica
                ON sw_cgm_pessoa_juridica.numcgm = participante_documentos.cgm_fornecedor
              JOIN licitacao.documento
                ON documento.cod_documento = participante_documentos.cod_documento
             WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
               AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
               AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")

        ";

        return $stSql;
    }

    public function recuperaCertidaoEConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaCertidaoEConta().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }



    public function montaRecuperaCertidaoEConta()
    {
        $stSql  = "
            SELECT 
                   CASE WHEN licitacao.cod_modalidade = 1 THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 2 THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 3 THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 4 THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 5 THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 6 THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 7 THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 8 THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                        WHEN licitacao.cod_modalidade = 9 THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                   END AS processo_licitatorio
                 , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                            sw_cgm_pessoa_fisica.cpf
                        ELSE
                            sw_cgm_pessoa_juridica.cnpj
                   END AS cpf_cnpj
                 , tipo_certidao_documento.cod_tipo_certidao AS tipo_certidao
                 , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                            1
                        ELSE
                            2
                   END AS tipo_pessoa
                 , CASE WHEN documento.cod_documento = 4 THEN substr(participante_documentos.num_documento,5)
                        ELSE participante_documentos.num_documento
                   END AS num_documento
                 , TO_CHAR(participante_documentos.dt_emissao, 'yyyymmdd') AS dt_emissao
                 , TO_CHAR(participante_documentos.dt_validade, 'yyyymmdd') AS dt_validade
            FROM licitacao.licitacao                
            JOIN licitacao.participante
                ON  participante.cod_licitacao  = licitacao.cod_licitacao
                AND participante.cod_modalidade = licitacao.cod_modalidade
                AND participante.cod_entidade   = licitacao.cod_entidade
                AND participante.exercicio      = licitacao.exercicio       
            JOIN licitacao.participante_documentos
                ON participante_documentos.cod_licitacao  = participante.cod_licitacao
                AND participante_documentos.cgm_fornecedor = participante.cgm_fornecedor
                AND participante_documentos.cod_modalidade = participante.cod_modalidade
                AND participante_documentos.cod_entidade   = participante.cod_entidade
                AND participante_documentos.exercicio      = participante.exercicio
            JOIN tceam.tipo_certidao_documento
                ON tipo_certidao_documento.cod_documento = participante_documentos.cod_documento
            JOIN sw_cgm
                ON sw_cgm.numcgm = participante_documentos.cgm_fornecedor
            LEFT JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = participante_documentos.cgm_fornecedor
            LEFT JOIN sw_cgm_pessoa_juridica
                ON sw_cgm_pessoa_juridica.numcgm = participante_documentos.cgm_fornecedor
            JOIN licitacao.documento
                ON documento.cod_documento = participante_documentos.cod_documento
            WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
               AND to_char(participante.dt_inclusao,'mm') = '".$this->getDado('mes')."'
               AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
        ";

        return $stSql;
    }



}
?>
