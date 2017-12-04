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
    * Arquivo ITextBoxSelectNaturezaFiscalizacao
    * Data de Criação: 24/07/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class IMontaAtividade extends Objeto
{
    //Define o objeto SelectMultiplo para armazenar os ELEMENTOS
    public function IMontaAtividade()
    {
    $rsAtividadesDisponiveis = $rsAtividadesSelecionadas = new recordSet;
    //Define o objeto SelectMultiplo para armazenar os ELEMENTOS
      $this->obCmbAtividades = new SelectMultiplo();
      $this->obCmbAtividades->setName   ("inCodigoAtividadesSelecionadas");
      $this->obCmbAtividades->setRotulo ( "Atividade" );
      $this->obCmbAtividades->setTitle  ( "Selecione a(s) Atidade(s)." );
      $this->obCmbAtividades->setNull   ( false );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    //if ($rsAtidadesDisponiveis->getNumLinhas()==1) {
               //$rsAtidadesSelecionadas = $rsAtidadesDisponiveis;
           //	$rsAtidadesDisponiveis = new RecordSet;
    //}

    // lista de atributos disponiveis
      $this->obCmbAtividades->SetNomeLista1 ('inCodigoAtividadesDisponiveis');
      $this->obCmbAtividades->setCampoId1   ( 'cod_atividade' );
      $this->obCmbAtividades->setCampoDesc1 ( 'nom_atividade' );
      $this->obCmbAtividades->SetRecord1    ( $rsAtividadesDisponiveis );

    // lista de atributos selecionados
      $this->obCmbAtividades->SetNomeLista2 ('inCodigoAtividadesSelecionadas');
      $this->obCmbAtividades->setCampoId2   ('cod_atividade');
      $this->obCmbAtividades->setCampoDesc2 ('nom_atividade');
      $this->obCmbAtividades->SetRecord2    ( $rsAtividadesSelecionadas );

    }

    public function geraFormulario(&$obFormulario)
    {
               $obFormulario->addComponente  ( $this->obCmbAtividades );
    }

}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
