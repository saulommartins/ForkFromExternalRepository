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
 * Classe de mapeamento para o cabeçalho dos relatórios gerados pelo MPDF
 * Data de Criação: 29/07/2014
 * 
 * @author Analista:      Eduardo Paculski Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes
 *
 * $Id: TAdministracaoMPDF.class.php 59612 2014-09-02 12:00:51Z gelson $
 * 
 * $Revision: 59612 $
 * $Name$
 * $Author: gelson $
 * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
 * 
 */
class TAdministracaoMPDF extends Persistente
{
    public function TAdministracaoMPDF()
    {
        parent::Persistente();
    }
    
    public function recuperaDadosRelatorio(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        
        $stSQL = $this->montaRecuperaDadosRelatorio($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        
        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);        
    }
    
    public function montaRecuperaDadosRelatorio()
    {
        $stSql = "
        SELECT nom_acao
             , nom_funcionalidade
             , nom_modulo
             , versao
          FROM administracao.acao
             , administracao.funcionalidade
             , administracao.modulo
             , administracao.gestao
        WHERE cod_acao = ".$this->getDado('cod_acao')."
          AND acao.cod_funcionalidade = funcionalidade.cod_funcionalidade
          AND funcionalidade.cod_modulo = modulo.cod_modulo
          AND modulo.cod_gestao = gestao.cod_gestao
        
        ";
        
        return $stSql;
    }
    
    public function recuperaDadosEntidade(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        
        $stSQL = $this->montaRecuperaDadosEntidade($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        
        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);        
    }
    
    public function montaRecuperaDadosEntidade()
    {
        $stSql = "
       SELECT sw_cgm.numcgm  AS num_entidade
            , sw_cgm.nom_cgm AS nom_entidade
            , sw_cgm.e_mail
            , sw_cgm.fone_comercial AS fone
            , sw_cgm.tipo_logradouro || ' ' || sw_cgm.logradouro || ', ' || sw_cgm.numero || ', ' || sw_cgm.bairro || ' - ' || sw_cgm.complemento AS logradouro
            , SUBSTR(sw_cgm.cep,1,5) || '-' || SUBSTR(sw_cgm.cep,6,3) AS cep
            , CASE WHEN ( sw_cgm_pessoa_juridica.cnpj IS NOT NULL )
                   THEN substr(sw_cgm_pessoa_juridica.cnpj,1,2)||'.'||substr(sw_cgm_pessoa_juridica.cnpj,3,3)||'.'||substr(sw_cgm_pessoa_juridica.cnpj,6,3)||'/'||substr(sw_cgm_pessoa_juridica.cnpj,9,4)||'-'||substr(sw_cgm_pessoa_juridica.cnpj,13,2) 
                   ELSE ''
              END AS cnpj
            , CASE WHEN ( entidade_logotipo.logotipo IS NOT NULL )
                   THEN '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/anexos/' || entidade_logotipo.logotipo
                   ELSE (  SELECT '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/' || valor AS logotipo 
                             FROM administracao.configuracao 
                            WHERE parametro = 'logotipo' 
                              AND exercicio = entidade.exercicio
                        )
              END AS logotipo 
         FROM orcamento.entidade 
   INNER JOIN sw_cgm
           ON sw_cgm.numcgm = entidade.numcgm
    LEFT JOIN orcamento.entidade_logotipo
           ON entidade_logotipo.exercicio = entidade.exercicio
          AND entidade_logotipo.cod_entidade = entidade.cod_entidade
    LEFT JOIN sw_cgm_pessoa_juridica
           ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
        WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
          AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."  
        ";
        
        return $stSql;
    }
    
}

?>