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
  * Classe de mapeamento da tabela DOCUMENTODINAMICO.DOCUMENTO_BLOCO_TEXTO
  * Data de Criação: 08/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

Casos de uso: uc-01.01.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  DOCUMENTODINAMICO.DOCUMENTO_BLOCO_TEXTO
  * Data de Criação: 08/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TDocumentoDinamicoDocumentoBlocoTexto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDocumentoDinamicoDocumentoBlocoTexto()
{
    parent::Persistente();
    $this->setTabela('documentoDinamico.documento_bloco_texto');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_documento,cod_bloco');

    $this->AddCampo('cod_documento','integer',true,'',true,true);
    $this->AddCampo('cod_bloco','integer',true,'',true,true);

}
function MontaRecuperaRelacionamento()
{
   $stSql .= "  SELECT                                                                 \n";
   $stSql .= "     BT.cod_bloco,                                                       \n";
   $stSql .= "     BT.texto,                                                           \n";
   $stSql .= "     BT.alinhamento                                                      \n";
   $stSql .= "  FROM                                                                   \n";
   $stSql .= "     ".$this->getTabela()."                         AS DBT,              \n";
   $stSql .= "     documentoDinamico.bloco_texto              AS BT                \n";
   $stSql .= "  WHERE                                                                  \n";
   $stSql .= "     BT.cod_bloco = DBT.cod_bloco                                        \n";
   $stSql .= "     And DBT.cod_documento = ".$this->getDado('cod_documento')."         \n";

   return $stSql;

}
}
