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
  * Classe de mapeamento da tabela ECONOMICO.DOMICILIO_INFORMADO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMDomicilioInformado.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.3  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.DOMICILIO_INFORMADO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMDomicilioInformado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMDomicilioInformado()
{
    parent::Persistente();
    $this->setTabela('economico.domicilio_informado');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,timestamp');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    //$this->AddCampo('inscricao_municipal','integer',true,'',false,true);
    $this->AddCampo('cod_logradouro','integer',true,'',false,true);
    $this->AddCampo('numero','varchar',true,'6',false,true);
    $this->AddCampo('complemento','varchar',true,'160',false,true);
    $this->AddCampo('cod_bairro','integer',true,'',false,true);
    $this->AddCampo('cep','varchar',true,'8',false,true);
    $this->AddCampo('caixa_postal','varchar',true,'6',false,true);
    $this->AddCampo('cod_municipio','integer',true,'',false,true);
    $this->AddCampo('cod_uf','integer',true,'',false,true);

}

function recuperaDomicilioInformado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDomicilioInformado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    //echo $stSql; exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDomicilioInformado()
{
    $stSql  = "SELECT                                                                   \n";
    $stSql .= "    di.inscricao_economica,                                               \n";
    $stSql .= "    di.timestamp,                                               \n";
    $stSql .= "    nl.nom_logradouro,                                                    \n";
    $stSql .= "    mu.nom_municipio,                                                     \n";
    $stSql .= "    uf.nom_uf,                                                     \n";
    $stSql .= "    di.numero,                                                            \n";
    $stSql .= "    di.cod_logradouro,                                                    \n";
    $stSql .= "    di.complemento,                                                       \n";
    $stSql .= "    di.cod_bairro,                                                        \n";
    $stSql .= "    di.cod_uf,                                                            \n";
    $stSql .= "    di.cod_municipio,                                                     \n";
    $stSql .= "    di.caixa_postal,                                                      \n";
    $stSql .= "    di.cep                                                                \n";
    $stSql .= "FROM                                                                      \n";
    $stSql .= "    economico.domicilio_informado as di,                                  \n";
    $stSql .= "    economico.cadastro_economico as ce,                                   \n";
    $stSql .= "    sw_municipio as mu,                                                   \n";
    $stSql .= "    sw_uf as uf,                                                          \n";
    $stSql .= "    sw_nome_logradouro nl                                                 \n";
    $stSql .= "WHERE                                                                     \n";
    $stSql .= "    ce.inscricao_economica   = di.inscricao_economica    AND              \n";
    $stSql .= "    di.cod_municipio         = mu.cod_municipio          AND              \n";
    $stSql .= "    di.cod_uf                = mu.cod_uf                 AND              \n";
    $stSql .= "    di.cod_uf                = uf.cod_uf                 AND              \n";
    $stSql .= "    nl.cod_logradouro        = di.cod_logradouro                          \n";

    return $stSql;
}

}
