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
    * Extensão da Classe de mapeamento
    * Data de Criação: 12/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 26070 $
    $Name$
    $Author: diego $
    $Date: 2007-10-13 18:51:13 -0300 (SÃ¡b, 13 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 12/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNReceita()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaRelacionamento()
{
    $stSql .= "
          SELECT   case when substr(cod_tc,1,1) = '9'
                        then substr(replace(cod_tc,'.',''),1,9)
                        else substr(replace(cod_tc,'.',''),1,8)||' '
                        end as estrutural
                  ,lpad(replace(coalesce(sum(valor),000),'.',''), 14,'0') as receita_bimestre
                  ,lpad(replace(coalesce(sum(valor_exercicio),000),'.',''), 14,'0') as receita_exercicio
                  ,lpad(replace(coalesce(max(vl_original),000),'.',''), 14,'0') as valor_previsto
          FROM
           tcern.fn_exportacao_receita('".$this->getDado('exercicio')."',
                                  '".$this->getDado('inCodEntidade')."',
                                   ".$this->getDado('inBimestre').")
          AS tabela              ( cod_estrutural varchar,
                                   cod_tc char(9),
                                   --data date,
                                   valor numeric,
                                   valor_exercicio numeric,
                                   vl_original numeric(14,2)
                                 )
          WHERE contabilidade.fn_tipo_conta_plano('".$this->getDado('exercicio')."',cod_estrutural)='A'
          GROUP BY
            tabela.cod_tc
          ORDER BY tabela.cod_tc
    ";

    return $stSql;
}

}
