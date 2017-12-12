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
  * Classe de mapeamento da tabela CONCURSO.CANDIDATO
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONCURSO.CANDIDATO
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TConcursoCandidato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela('concurso.candidato');

    $this->setCampoCod('cod_candidato');
    $this->setComplementoChave('');

    $this->AddCampo('cod_candidato',    'integer',true,  '',    true, false  );
    $this->AddCampo('classificacao',    'integer',false, '',    false,false );
    $this->AddCampo('numcgm',           'integer',true,  '',    false,true  );
    $this->AddCampo('nota_prova',       'numeric',false, '10.2',false,false );
    $this->AddCampo('nota_titulacao',   'numeric',false, '10.2',false,false );
    $this->AddCampo('reclassificado',  'boolean',false, '',    false,false );

}

public function montaRecuperaCandidatoConcurso()
{
    
    $stSql  = " 
     SELECT ccc.cod_edital
          , c.tipo_prova
          , cc.cod_candidato
          , c.tipo_prova
          , cgm.nom_cgm
          , cc.numcgm
          , c.avalia_titulacao
          , c.nota_minima
          , coalesce((cc.classificacao),0) AS classificacao
          , cc.nota_prova,cc.nota_titulacao
          , t.media
          , cgm.logradouro,cgm.numero
          , cgm.complemento
          , cgm.bairro
          , cgm.cep
          , municipio.nom_municipio
          , uf.sigla_uf
          , cgm.fone_residencial
          , fone_celular
          , CASE cc.reclassificado                                                                  
              WHEN 't' THEN 'true'                                                            
              WHEN 'f' THEN 'false'
            END as reclassificado,                                    
            CASE cc.reclassificado                                                                  
                 WHEN 't' THEN '/Reclassificado'                                                 
                 WHEN 'f' THEN  null END as reclassificacao
          , CASE WHEN t.media >= c.nota_minima THEN 'Aprovado'                                      
              WHEN t.media < c.nota_minima AND t.media IS NOT NULL THEN 'Reprovado'             
              WHEN t.media is null  THEN 'Sem nota' END as situacao                              
       
       FROM concurso.concurso_candidato ccc
    
       INNER JOIN concurso.candidato cc
               ON ccc.cod_candidato = cc.cod_candidato
       
       INNER JOIN concurso.edital c
               ON c.cod_edital = ccc.cod_edital                                                       
         
       INNER JOIN sw_cgm cgm
               ON cc.numcgm = cgm.numcgm
           
        INNER JOIN sw_municipio municipio
                ON cgm.cod_municipio = municipio.cod_municipio                                         
               AND cgm.cod_uf =  municipio.cod_uf                                                      
             
        INNER JOIN sw_uf uf
                ON cgm.cod_uf = uf.cod_uf                                                              
         
         INNER JOIN (
               SELECT cca.cod_candidato as cod_candidato
                    , CASE c.avalia_titulacao                                                             
                         WHEN 't' THEN round((cca.nota_titulacao + cca.nota_prova)/2,2)                  
                         WHEN 'f' THEN cca.nota_prova                                                    
                      END AS media
                FROM concurso.edital c
                   , concurso.concurso_candidato cc
                   , concurso.candidato cca                                                              
               WHERE c.cod_edital = cc.cod_edital
                 AND cc.cod_candidato = cca.cod_candidato
             ) AS t   
            ON t.cod_candidato = cc.cod_candidato
         
         WHERE 1 = 1 ";
         
return $stSql;

}

/*
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
public function recuperaCandidatoConcurso(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = ' order by ' . $stOrdem;
    $stSql = $this->montaRecuperaCandidatoConcurso().$stFiltro.$stOrdem;
    $stSql = "select * from ( " . $stSql . ") as tabela order by situacao";
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}

?>