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
     * Classe de mapeamento para a tabela IMOBILIARIO.IMOVEL_CORRESPONDENCIA
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMImovelCorrespondencia.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.11  2007/01/10 14:55:07  cercato
Bug #8033#

Revision 1.10  2006/10/26 16:01:24  dibueno
Alterações nos campos chave da tabela

Revision 1.9  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.IMOVEL_CORRESPONDENCIA
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMImovelCorrespondencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMImovelCorrespondencia()
{
    parent::Persistente();
    $this->setTabela('imobiliario.imovel_correspondencia');

    //$this->setCampoCod('inscricao_municipal');
    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,cod_uf, cod_municipio, cod_logradouro');

    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('cod_uf','integer',true,'',false,true);
    $this->AddCampo('cod_municipio','integer',true,'',false,true);
    $this->AddCampo('cod_bairro','integer',true,'',false,true);
    $this->AddCampo('cod_logradouro','integer',true,'',false,true);
    $this->AddCampo('cep','varchar',true,'8',false,false);
    $this->AddCampo('numero','varchar',true,'6',false,false);
    $this->AddCampo('complemento','varchar',true,'160',false,false);
    $this->AddCampo('caixa_postal','varchar',false,'6',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}
function montaRecuperaRelacionamento()
{
    $stSQL = "SELECT                                               \n";
    $stSQL .= "    IC.*,                                           \n";
    $stSQL .= "    TO_CHAR(IC.timestamp,'dd/mm/yyyy') AS data,     \n";
    $stSQL .= "    L.nom_logradouro,                               \n";
    $stSQL .= "    M.nom_municipio,                                \n";
    $stSQL .= "    U.sigla_uf,                                     \n";
    $stSQL .= "    U.nom_uf,                                       \n";
    $stSQL .= "    B.nom_bairro,                                   \n";
    $stSQL .= "    TL.nom_tipo                                     \n";
    $stSQL .= "FROM                                                \n";
    $stSQL .= "    imobiliario.imovel_correspondencia IC,          \n";
    $stSQL .= "    sw_nome_logradouro L,                           \n";
    $stSQL .= "    sw_tipo_logradouro TL,                          \n";
    $stSQL .= "    sw_municipio M,                                 \n";
    $stSQL .= "    sw_uf U,                                        \n";
    $stSQL .= "    sw_bairro B                                     \n";
    $stSQL .= "WHERE                                               \n";
    $stSQL .= "    IC.cod_logradouro = L.cod_logradouro AND        \n";
    $stSQL .= "    TL.cod_tipo = L.cod_tipo AND                    \n";
    $stSQL .= "    IC.cod_municipio = M.cod_municipio AND          \n";
    $stSQL .= "    IC.cod_uf = U.cod_uf AND                        \n";
    $stSQL .= "    IC.cod_bairro = B.cod_bairro AND                \n";
    $stSQL .= "    M.cod_uf = U.cod_uf AND                         \n";
    $stSQL .= "    B.cod_municipio = M.cod_municipio AND           \n";
    $stSQL .= "    B.cod_uf = U.cod_uf                             \n";

    return $stSQL;
}
}
