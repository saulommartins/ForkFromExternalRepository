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
    * Classe de mapeamento da tabela licitacao.edital
    * Data de Criação: 14/01/2009

    * @author Analista:      Gelson W. Gonçalves  <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Diogo Zarpelon       <diogo.zarpelon@cnm.org.br>

    * @package    URBEM
    * @subpackage Mapeamento

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoAta extends Persistente
{
  # Construct
  public function TLicitacaoAta()
  {
    parent::Persistente();

    $this->setTabela("licitacao.ata");

    $this->setCampoCod('id');

    $this->AddCampo('id'             , 'sequence'  , true , ''  , true  , false             );
    $this->AddCampo('num_ata'        , 'integer'   , true , ''  , false , false             );
    $this->AddCampo('exercicio_ata'  , 'varchar'   , true , '4' , false , false             );
    $this->AddCampo('num_edital'     , 'integer'   , true , ''  , false , 'TLicitacaoEdital');
    $this->AddCampo('exercicio'      , 'varchar'   , true , '4' , false , 'TLicitacaoEdital');
    $this->AddCampo('timestamp'      , 'timestamp' , true , ''  , false , false             );
    $this->AddCampo('descricao'      , 'text'      , true , ''  , false , false             );
    $this->AddCampo('dt_validade_ata', 'date'      , true , ''  , false , false             );
    $this->AddCampo('tipo_adesao'    , 'integer'   , true , ''  , false , false             );
  }

  public function recuperaAta(&$rsAta, $stFiltro='', $stOrdem='')
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaAta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL($rsAta, $stSql, $boTransacao);

    return $obErro;
  }

  public function montaRecuperaAta()
  {
    $stSql .= "  SELECT  id                                                     \n";
    $stSql .= "       ,  num_ata                                                \n";
    $stSql .= "       ,  exercicio_ata                                          \n";
    $stSql .= "       ,  num_edital                                             \n";
    $stSql .= "       ,  exercicio                                              \n";
    $stSql .= "       ,  timestamp                                              \n";
    $stSql .= "       ,  to_char(cast(timestamp as date),'DD/MM/YYYY') as date  \n";
    $stSql .= "       ,  descricao                                              \n";
    $stSql .= "       ,  to_char(cast(dt_validade_ata as date),'DD/MM/YYYY') as date_valida  \n";
    $stSql .= "       ,  tipo_adesao                                              \n";
    $stSql .= "                                                                 \n";
    $stSql .= "    FROM  ".$this->getTabela()."                                 \n";
    $stSql .= "                                                                 \n";
    $stSql .= "   WHERE  1=1                                                    \n";

    if ($this->getDado('id'))
      $stSql .= "   AND  id = ".$this->getDado('id');

    if ($this->getDado('num_ata'))
      $stSql .= "   AND num_ata = ".$this->getDado('num_ata');

    if ($this->getDado('exercicio_ata'))
      $stSql .= "   AND exercicio_ata = '".$this->getDado('exercicio_ata')."'";

    if ($this->getDado('num_edital') != '')
      $stSql .= "   AND num_edital = ".$this->getDado('num_edital');

    if ($this->getDado('exercicio'))
      $stSql .= "   AND exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
  }

}
