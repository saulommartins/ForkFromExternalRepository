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
    * Gerar o componente o SelectMultiplo com a Lotação
    * Data de Criação: 21/02/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.00

    $Id: ISelectMultiploClassificacaoAssentamento.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

/**
    * Gerar o componente o SelectMultiplo com a Lotação
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploClassificacaoAssentamento extends SelectMultiplo
{
/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploClassificacaoAssentamento()
{
    parent::SelectMultiplo();
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php");
    $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
    $obTPessoalClassificacaoAssentamento->recuperaTodos($rsDisponiveis,""," descricao");

    $rsSelecionados = new Recordset;
    $this->setName       ( "inCodClassificacaoAssentamento"                             );
    $this->setRotulo     ( "Classificação Assentamento"                                 );
    $this->setTitle      ( "Selecione a classificação do assentamento para o filtro"    );
    $this->setNomeLista1 ( "inCodClassificacaoAssentamentoDisponiveis"                  );
    $this->setRecord1    ( $rsDisponiveis                                               );
    $this->setCampoId1   ( "[cod_classificacao]"                                        );
    $this->setCampoDesc1 ( "[descricao]"                                                );
    $this->setStyle1     ( "width:300px"                                                );
    $this->setNomeLista2 ( "inCodClassificacaoAssentamentoSelecionados"                 );
    $this->setRecord2    ( $rsSelecionados                                              );
    $this->setCampoId2   ( "[cod_classificacao]"                                        );
    $this->setCampoDesc2 ( "[descricao]"                                                );
    $this->setStyle2     ( "width:300px"                                                );

}
}
?>
