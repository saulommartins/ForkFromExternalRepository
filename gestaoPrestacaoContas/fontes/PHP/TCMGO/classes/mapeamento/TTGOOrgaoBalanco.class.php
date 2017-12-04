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
    * Data de Criação:

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.1  2007/04/24 13:50:31  vitor
Inclusão uc-06.04.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOOrgaoBalanco extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TTGOOrgaoBalanco()
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
            ,  nom_orgao
            ,  cod_tipo
            ,  cgm_orgao_juridica.cnpj
            ,  cgm_gestor.nom_cgm AS gestor
            ,  ( BTRIM(cgm_gestor.logradouro) || ' ' || BTRIM(cgm_gestor.numero) || ' ' || BTRIM(cgm_gestor.complemento) ) AS logradouro
            ,  BTRIM(cgm_gestor.bairro) AS setor
            ,  cgm_gestor.cep
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
       WHERE  tcmgo.orgao.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }
}
