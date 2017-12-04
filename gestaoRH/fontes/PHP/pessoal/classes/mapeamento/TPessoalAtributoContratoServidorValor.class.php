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
  * Classe de mapeamento da tabela ATRIBUTO_CONTRATO_SERVIDOR_VALOR
  * Data de Criação: 18/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ATRIBUTO_CONTRATO_SERVIDOR_VALOR
  * Data de Criação: 18/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAtributoContratoServidorValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAtributoContratoServidorValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('pessoal.atributo_contrato_servidor_valor');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_modulo,cod_atributo,cod_cadastro,timestamp');

    $this->AddCampo('cod_contrato','integer'       ,true, '', true,true  );
    $this->AddCampo('cod_atributo','integer'       ,true, '', true,true  );
    $this->AddCampo('cod_cadastro','integer'       ,true, '', true,true  );
    $this->AddCampo('valor'       ,'text'          ,true, '',false,false  );
    $this->AddCampo('timestamp'   ,'timestamp_now' ,true, '', true,false );
    $this->AddCampo('cod_modulo'  ,'integer'       ,true, '', true,true  );
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT atributo_contrato_servidor_valor.*                                                                   \n";
    $stSql .= "     , CASE WHEN atributo_dinamico.cod_tipo = 4 THEN                                                        \n";
    $stSql .= "        (SELECT atributo_valor_padrao.valor_padrao                                                          \n";
    $stSql .= "          FROM administracao.atributo_valor_padrao                                                          \n";
    $stSql .= "         WHERE atributo_valor_padrao.cod_modulo   = atributo_dinamico.cod_modulo                            \n";
    $stSql .= "           AND atributo_valor_padrao.cod_cadastro = atributo_dinamico.cod_cadastro                          \n";
    $stSql .= "           AND atributo_valor_padrao.cod_atributo = atributo_dinamico.cod_atributo                          \n";
    $stSql .= "           AND atributo_valor_padrao.cod_valor = atributo_contrato_servidor_valor.valor)                    \n";
    $stSql .= "        ELSE '' END AS valor_padrao                                                                         \n";
    $stSql .= "  FROM pessoal.atributo_contrato_servidor_valor                                                             \n";
    $stSql .= "     , (SELECT cod_contrato                                                                                 \n";
    $stSql .= "             , cod_atributo                                                                                 \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                  \n";
    $stSql .= "          FROM pessoal.atributo_contrato_servidor_valor                                                     \n";
    $stSql .= "        GROUP BY cod_contrato                                                                               \n";
    $stSql .= "               , cod_atributo) as max_atributo_contrato_servidor_valor                                      \n";
    $stSql .= "     , administracao.atributo_dinamico                                                                      \n";
    $stSql .= " WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato    \n";
    $stSql .= "   AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo    \n";
    $stSql .= "   AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp          \n";
    $stSql .= "   AND atributo_contrato_servidor_valor.cod_modulo   = atributo_dinamico.cod_modulo                         \n";
    $stSql .= "   AND atributo_contrato_servidor_valor.cod_cadastro = atributo_dinamico.cod_cadastro                       \n";
    $stSql .= "   AND atributo_contrato_servidor_valor.cod_atributo = atributo_dinamico.cod_atributo                       \n";

    return $stSql;
}

}
