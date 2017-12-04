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
* Classe de mapeamento para administracao.atributo_valor_padrao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18763 $
$Name$
$Author: cassiano $
$Date: 2006-12-14 08:56:11 -0200 (Qui, 14 Dez 2006) $

Casos de uso: uc-01.03.96
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.ATRIBUTO_VALOR_PADRAO
  * Data de Criação: 09/08/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoAtributoValorPadrao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoAtributoValorPadrao()
{
    parent::Persistente();
    $this->setTabela('administracao.atributo_valor_padrao');

    $this->setCampoCod('cod_valor');
    $this->setComplementoChave('cod_modulo,cod_cadastro,cod_atributo');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_valor','integer',true,'',true,false);
    $this->AddCampo('ativo','bool',true,'',false,false);
    $this->AddCampo('valor_padrao','varchar',true,'1000',false,false);

}

function montaRecuperaAtributosValor()
{
        $stSql  = " SELECT
                            atributo_valor_padrao.cod_modulo
                            atributo_valor_padrao.cod_cadastro
                            atributo_valor_padrao.cod_atributo
                            atributo_valor_padrao.cod_valor
                            atributo_valor_padrao.ativo
                            atributo_valor_padrao.valor_padrao
                            atributo_dinamico.cod_tipo
                            atributo_dinamico.nom_atributo
                    FROM
                            administracao.atributo_dinamico
                    JOIN    administracao.atributo_valor_padrao
                      ON    atributo_valor_padrao.cod_modulo = atributo_dinamico.cod_modulo
                     AND    atributo_valor_padrao.cod_cadastro = atributo_dinamico.cod_cadastro
                     AND    atributo_valor_padrao.cod_atributo = atributo_dinamico.cod_atributo";

        return $stSql;
    }

}
