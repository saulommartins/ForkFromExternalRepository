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
    * Classe de mapeamento da tabela CONTABILIDADE.PLANO_ANALITICA_CREDITO
    * Data de Criação: 12/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.02
                    uc-02.04.03
*/

/*
$Log$
Revision 1.9  2007/05/29 14:12:51  domluc
Mudanças na forma de classificação de receitas.

Revision 1.8  2007/03/09 15:37:13  domluc
uc-02.04.33

Revision 1.7  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.PLANO_ANALITICA_CREDITO
  * Data de Criação: 12/09/2005

  * @author Analista: Lucas Leusin
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadePlanoAnaliticaCredito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('contabilidade.plano_analitica_credito');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_plano');

    $this->AddCampo( 'cod_plano'   ,'integer',true,  '', true,true );
    $this->AddCampo( 'exercicio'   ,   'char',true,'04', true,true );
    $this->AddCampo( 'cod_especie' ,'integer',true,  '',false,true );
    $this->AddCampo( 'cod_genero'  ,'integer',true,  '',false,true );
    $this->AddCampo( 'cod_natureza','integer',true,  '',false,true );
    $this->AddCampo( 'cod_credito' ,'integer',true,  '',false,true );

}

function montaRecuperaVerificaCredito()
{
    $stSql = " select * from contabilidade.plano_analitica_credito";
    $stSql .= " where ";
    $stSql .= "  cod_credito = " . $this->getDado('cod_credito');
    $stSql .= "  and cod_especie = " . $this->getDado('cod_especie');
    $stSql .= "  and cod_genero = " . $this->getDado('cod_genero');
    $stSql .= "  and cod_natureza = " . $this->getDado('cod_natureza');
    $stSql .= "  and cod_plano = " . $this->getDado('cod_plano');
    $stSql .= "  and exercicio = '" . $this->getDado('exercicio')."'";
    $stSql .= "  ";

    return $stSql;
}

/**
 * Valida credito/acrescimo, verificando se ja não esta vinculado a outra receita/conta
 */
function recuperaClassReceitasCreditosValidacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaClassReceitasCreditosValidacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaClassReceitasCreditosValidacao()
{
    $stSql = "  select tbl.cod_credito ";
    $stSql .= "   from orcamento.receita_credito as tbl ";
    $stSql .= "	where tbl.cod_credito = " . $this->getDado('cod_credito') ."
                                and tbl.cod_especie = " . $this->getDado('cod_especie') ."
                                and tbl.cod_genero = " . $this->getDado('cod_genero') ."
                                and tbl.cod_natureza = " . $this->getDado('cod_natureza') ."";
    return $stSql;
}

}
