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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOOrgao.class.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOOrgao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.orgao");

        $this->setCampoCod('num_orgao');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'num_orgao' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio','varchar' ,true, '4' ,true,true );
        $this->AddCampo( 'numcgm_orgao','integer' ,true, '' ,false,true );
        $this->AddCampo( 'numcgm_contador','integer' ,true, '' ,false,true );
        $this->AddCampo( 'cod_tipo','integer' ,true, '' ,false,true );
        $this->AddCampo( 'crc_contador','varchar' ,true, '11' ,false,false );
        $this->AddCampo( 'uf_crc_contador','varchar' ,true, '2' ,false,false );
    }

    public function recuperaOrgao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaOrgao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgao()
    {
        $stSql = "
    SELECT
               '10' AS tipo_registro
            ,  tcmgo.orgao.num_orgao
            ,  cgm_gestor_fisica.cpf
            ,  cgm_gestor.fone_comercial as fone_gestor
            ,  cgm_gestor.e_mail as email_gestor
            ,  to_char(dt_inicio,'dd/mm/yyyy') as dt_inicio
            ,  to_char(dt_fim,'dd/mm/yyyy') as dt_fim
            ,  cod_tipo
            ,  cgm_orgao_juridica.cnpj
            ,  remove_acentos(nom_orgao) as nom_orgao
            ,  remove_acentos(cgm_gestor.nom_cgm) AS gestor
            ,  ( BTRIM(cgm_gestor.logradouro) || ' ' || BTRIM(cgm_gestor.numero) || ' ' || BTRIM(cgm_gestor.complemento) ) AS logradouro
            ,  BTRIM(cgm_gestor.bairro) AS setor
            ,  BTRIM(nom_municipio) AS cidade
            ,  sigla_uf
            ,  cgm_gestor.cep
            ,  BTRIM(cgm_contador.nom_cgm) AS nom_contador
            ,  cgm_contador_fisica.cpf AS cpf_contador
            ,  BTRIM(tcmgo.orgao.crc_contador) AS crc_contador
            ,  BTRIM(tcmgo.orgao_gestor.cargo) AS cargo_gestor
            ,  cgm_contador.fone_comercial as fone_contador
            ,  tcmgo.orgao.uf_crc_contador
            ,  cgm_controle_interno.nom_cgm AS nom_controle_interno
            ,  (   CASE    WHEN cgm_controle_interno.nom_cgm ISNULL
                           THEN ''
                           ELSE cgm_controle_interno_fisica.cpf
                   END     ) as cpf_controle_interno
            ,  cgm_representante.nom_cgm AS nom_representante
            ,  cgm_representante_fisica.cpf AS cpf_representante
            ,  0 AS numero_sequencial
        FROM  orcamento.orgao
  INNER JOIN  tcmgo.orgao
          ON  tcmgo.orgao.num_orgao = orcamento.orgao.num_orgao
         AND  tcmgo.orgao.exercicio = orcamento.orgao.exercicio
  INNER JOIN  tcmgo.orgao_gestor
          ON  tcmgo.orgao_gestor.exercicio = tcmgo.orgao.exercicio
         AND  tcmgo.orgao_gestor.num_orgao = tcmgo.orgao.num_orgao
  INNER JOIN  sw_cgm AS cgm_gestor
          ON  cgm_gestor.numcgm = tcmgo.orgao_gestor.numcgm
  INNER JOIN  sw_cgm_pessoa_fisica AS cgm_gestor_fisica
          ON  cgm_gestor_fisica.numcgm = cgm_gestor.numcgm
  inner join  sw_municipio
          ON  sw_municipio.cod_municipio = cgm_gestor.cod_municipio
         AND  sw_municipio.cod_uf = cgm_gestor.cod_uf
  INNER JOIN  sw_uf
          ON  sw_uf.cod_uf = cgm_gestor.cod_uf
  INNER JOIN  sw_cgm AS cgm_orgao
          ON  cgm_orgao.numcgm = tcmgo.orgao.numcgm_orgao
  INNER JOIN  sw_cgm_pessoa_juridica AS cgm_orgao_juridica
          ON  cgm_orgao_juridica.numcgm = cgm_orgao.numcgm
  INNER JOIN  sw_cgm as cgm_contador
          ON  cgm_contador.numcgm = tcmgo.orgao.numcgm_contador
  INNER JOIN  sw_cgm_pessoa_fisica AS cgm_contador_fisica
          ON  cgm_contador_fisica.numcgm = cgm_contador.numcgm
   LEFT JOIN  tcmgo.orgao_controle_interno
          ON  orgao_controle_interno.exercicio = orcamento.orgao.exercicio
         AND  orgao_controle_interno.num_orgao = orcamento.orgao.num_orgao
   LEFT JOIN  sw_cgm AS cgm_controle_interno
          ON  cgm_controle_interno.numcgm = orgao_controle_interno.numcgm
   LEFT JOIN  sw_cgm_pessoa_fisica AS cgm_controle_interno_fisica
          ON  cgm_controle_interno_fisica.numcgm = cgm_controle_interno.numcgm
   LEFT JOIN  tcmgo.orgao_representante
          ON  orgao_representante.exercicio = orcamento.orgao.exercicio
         AND  orgao_representante.num_orgao = orcamento.orgao.num_orgao
   LEFT JOIN  sw_cgm AS cgm_representante
          ON  cgm_representante.numcgm = orgao_representante.numcgm
   LEFT JOIN  sw_cgm_pessoa_fisica AS cgm_representante_fisica
          ON  cgm_representante_fisica.numcgm = cgm_representante.numcgm
       WHERE  tcmgo.orgao.exercicio = '".$this->getDado('exercicio')."'
         AND  to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') <= dt_fim
         AND  to_date('".$this->getDado('dtFim')."','dd/mm/yyyy') >= dt_inicio
        ";

        return $stSql;
    }

function recuperaUnidadeOrcamentaria(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUnidadeOrcamentaria();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUnidadeOrcamentaria()
{
$stSql = "
               SELECT unidade.exercicio
                    , unidade.num_unidade
                    , unidade.num_orgao
                    , orgao.nom_orgao || ' - ' || unidade.nom_unidade as nom_unidade
                 from orcamento.unidade
                 JOIN orcamento.orgao
                   on orgao.exercicio = unidade.exercicio
                  and orgao.num_orgao = unidade.num_orgao
                WHERE unidade.exercicio = '".$this->getDado('exercicio')."'
             ORDER BY unidade.num_unidade
                    , unidade.num_orgao ";

    return $stSql;
}

  public function recuperaRegistro10BLC(&$rsRecordSet, $boTransacao = "")
  {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaRegistro10BLC();
      $this->setDebug($stSql);
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
  }

  public function montaRecuperaRegistro10BLC()
  {
      $stSql = "
                     SELECT 10 AS tipo_registro
                          , num_orgao AS cod_orgao
                          , cod_tipo AS tipo_unidade_orcamentaria
                       from tcmgo.orgao
                      WHERE exercicio = '".$this->getDado('exercicio')."' ";

      return $stSql;
  }
}
