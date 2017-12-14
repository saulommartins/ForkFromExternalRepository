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
 * Componente que monta os combos de banco, agencia , conta e busca inner para o cheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_BUSCAINNER;
include_once CAM_GT_MON_COMPONENTES . 'IMontaAgenciaConta.class.php';

class IMontaContaCheque extends Componente
{
    public $obIMontaAgenciaConta,
        $obBscCheque,
        $stTipoBusca,
        $boVinculoPlanoBanco,
        $inCodEntidadeVinculo;

    /**
     * setVinculoPlanoBanco
     * Seta o valor a propriedade $boVinculoPlanoBanco que serve para identificar se é necessário ou não realizar o vinculo com a tabela
     * contabiliadade.plano_banco
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @param  boolean $boValue
     * @return void
     */
    public function setVinculoPlanoBanco($boValue)
    {
        $this->boVinculoPlanoBanco = $boValue;
    }

    /**
     * setCodEntidadeVinculo
     * Seta o valor a propriedade $inCodEntidadeVinculo que serve para filtrar a entidade necessária no vinculo com a tabela
     * contabiliadade.plano_banco. O valor setado aqui só será usado caso a propriedade $boVinculoPlanoBanco esteja setada como true
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @param  Integer $inValue
     * @return void
     */
    public function setCodEntidadeVinculo($inValue)
    {
        $this->inCodEntidadeVinculo = $inValue;
    }

    /**
     * getVinculoPlanoBanco
     * Retorna o valor a propriedade $boVinculoPlanoBanco
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @return boolean
     */
    public function getVinculoPlanoBanco()
    {
        return $this->boVinculoPlanoBanco;
    }

    /**
     * getCodEntidadeVinculo
     * Retorna o valor a propriedade $inCodEntidadeVinculo
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @return Integer
     */
    public function getCodEntidadeVinculo()
    {
        return $this->inCodEntidadeVinculo;
    }

    /**
     * Metodo construtor da classe IMontaContaCheque
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        //Isntancia o componente IMontaAgenciaConta
        $this->obIMontaAgenciaConta = new IMontaAgenciaConta();

        //Instancia o componete buscainner para o cheque
        $this->obBscCheque = new BuscaInner     ();
        $this->obBscCheque->setRotulo           ('Nr. Cheque'                );
        $this->obBscCheque->setTitle            ('Informe o número do cheque');
        $this->obBscCheque->setNull             (false                       );
        $this->obBscCheque->obCampoCod->setName ('stNumCheque'               );
        $this->obBscCheque->obCampoCod->setId   ('stNumCheque'               );
        $this->obBscCheque->obCampoCod->setSize (20                          );
        $this->obBscCheque->obCampoCod->setAlign('left'                      );
        $this->setVinculoPlanoBanco(false);
    }

    public function setObrigatorioBarra($valor = true)
    {
        $this->obBscCheque->setNull(true);
        $this->obBscCheque->setObrigatorioBarra(true);
        $this->obIMontaAgenciaConta->obBscConta->setNull(true);
        $this->obIMontaAgenciaConta->obBscConta->setObrigatorioBarra(true);
        $this->obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->setNull(true);
        $this->obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->setObrigatorioBarra(true);
        $this->obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setNull(true);
        $this->obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setObrigatorioBarra(true);
    }

    public function setTipoBusca($stTipoBusca)
    {
        $this->stTipoBusca = $stTipoBusca;
    }

    /**
     * Metodo que gera o formulario do componente
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @param object $obFormulario
     *
     * @return void
     */
    public function geraFormulario(&$obFormulario)
    {
        //Dados que serao passados por parametro
        $stParamsPopUp  = "?inCodBanco='+jq('#inCodBancoTxt').val()+'&stNumAgencia='+jq('#stNumAgenciaTxt').val()+'&";
        $stParamsPopUp .= "stNumeroConta='+jq('#stContaCorrente').val()+'&stCampoNum=" . $this->obBscCheque->obCampoCod->getId() . "&";
        $stParamsPopUp .= "stNomForm=" . $obFormulario->obForm->getName() . "&stTipoBusca=" . $this->stTipoBusca . "&";
        if ($this->getVinculoPlanoBanco()) {
            $stParamsPopUp .= "boVinculoPlanoBanco=true";
            $this->obIMontaAgenciaConta->setVinculoPlanoBanco (true);
            $inCodEntidade = $this->getCodEntidadeVinculo();
            if ($inCodEntidade != '') {
                $this->obIMontaAgenciaConta->setCodEntidadeVinculo($inCodEntidade);
            }
        }
        $stParamsBlur   = $stParamsPopUp . "&stNumCheque='+jq('#stNumCheque').val()+'";

        //Condicao para que a popup seja utilizada. O banco e a agencia e a conta corrente tem que estar setados
        $stCondicaoPopUp  = "ajaxJavaScript('".CAM_GF_TES_INSTANCIAS."processamento/OCIMontaContaCheque.php?','limpaFiltro');";
        $stCondicaoPopUp .= "if (jq('#stContaCorrente').val() == '') { alertaAviso('Informe a Conta Corrente','frm','erro','".Sessao::getId()."'); }";
        $stCondicaoPopUp .= "else{ abrePopUp('".CAM_GF_TES_POPUPS."cheques/LSProcurarCheque.php".$stParamsPopUp."','frm','stNumCheque','','','".Sessao::getId()."','800','550'); }";

        $this->obBscCheque->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".CAM_GF_TES_INSTANCIAS."processamento/OCIMontaContaCheque.php".$stParamsBlur."','buscaCheque');");

        $this->obBscCheque->setFuncaoBusca($stCondicaoPopUp);

        $this->obIMontaAgenciaConta->geraFormulario($obFormulario     );
        $obFormulario->addComponente               ($this->obBscCheque);
    }

}
