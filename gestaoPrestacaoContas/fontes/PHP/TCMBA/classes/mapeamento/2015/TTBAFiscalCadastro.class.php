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
    * Data de Criação: 02/10/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBAFiscalCadastro extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct ()
{
  parent::Persistente();
  $this->setEstrutura( array() );
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
    $stSql = " SELECT  1 AS tipo_registro
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , obra_fiscal.matricula
                     , sw_cgm.nom_cgm
                     , obra_fiscal.registro_profissional
                     , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf                                 
                            WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj                                
                            ELSE ''                                                              
                       END AS cpf_cnpj
                     , '".$this->getDado('exercicio').$this->getDado('mes')."' AS competencia
                      
                 FROM tcmba.obra_fiscal
                  
           INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = obra_fiscal.numcgm
            
            LEFT JOIN sw_cgm_pessoa_fisica
                   ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
            
            LEFT JOIN sw_cgm_pessoa_juridica
                   ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                    
                WHERE obra_fiscal.exercicio     = '".$this->getDado('exercicio')."'
                  AND obra_fiscal.cod_entidade IN ( ".$this->getDado('entidades')." )
                  AND TO_CHAR( obra_fiscal.data_inicio, 'MM/YYYY') <= TO_CHAR( TO_DATE('".$this->getDado('dt_final')."', 'DD/MM/YYYY'), 'MM/YYYY')
                  AND TO_CHAR( obra_fiscal.data_final, 'MM/YYYY')  >= TO_CHAR( TO_DATE('".$this->getDado('dt_inicial')."', 'DD/MM/YYYY'), 'MM/YYYY') ";
                    
           return $stSql;
}

}

?>